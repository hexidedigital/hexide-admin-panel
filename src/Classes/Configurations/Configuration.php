<?php

namespace HexideDigital\HexideAdmin\Classes\Configurations;


use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Configuration
{
    public const DefaultType = Configuration::TEXT;

    /** one-line text @var string */
    public const TEXT = 'text';
    /** multiline text @var string */
    public const TEXTAREA = 'textarea';
    /** multiline text with editor @var string */
    public const EDITOR = 'editor';
    /** day of week @var string */
    public const WEEKDAY = 'weekday';
    /** time @var string */
    public const TIME = 'time';
    /** date @var string */
    public const DATE = 'date';
    /** logic type @var string */
    public const BOOLEAN = 'boolean';
    /** list of items with one selectable @var string */
    public const SELECT = 'select';
    /** list of items with more selectable @var string */
    public const MULTI_SELECT = 'multi_select';
    /** image path @var string */
    public const IMAGE = 'image';
    /** file path @var string */
    public const FILE = 'file';
    /** array of range @var string */
    public const RANGE = 'range';
    /** banner with title, text, image and button @var string */
    public const IMG_BUTTON = 'img_button';

    /** @var array<string> */
    protected static array $types = [
        Configuration::TEXT,
        Configuration::TEXTAREA,
        Configuration::EDITOR,
        Configuration::WEEKDAY,
        Configuration::TIME,
        Configuration::DATE,
        Configuration::BOOLEAN,
        Configuration::SELECT,
        Configuration::MULTI_SELECT,
        Configuration::IMAGE,
        Configuration::FILE,
        Configuration::RANGE,
        Configuration::IMG_BUTTON,
    ];

    public function getTypes(): array
    {
        return static::$types;
    }

    public function implodeTypes(): string
    {
        return implode(',', static::$types);
    }

    public function getDeniedGroupNames(): array
    {
        return [
            'broadcasting',
            'adminlte',
            'app',
            'auth',
            'cache',
            'cors',
            'database',
            'filesystems',
            'hashing',
            // 'hexide-admin',
            'ide-helper',
            'jwt',
            'livewire',
            'livewire-tables',
            'logging',
            'mail',
            'model-permissions',
            'queue',
            'sanctum',
            'services',
            'session',
            'toastr',
            'translatable',
            'view',
        ];
    }

    public function getStoreKey(string $type, ?bool $translatable): string
    {
        if ($this->isTextValueType($type)) {
            return $translatable ? 'text' : 'content';
        }

        return $translatable ? 'json' : 'value';
    }

    public function isTextValueType(?string $type): bool
    {
        return in_array($type, [
            Configuration::TEXT,
            Configuration::TEXTAREA,
            Configuration::EDITOR,
            Configuration::TIME,
            Configuration::DATE,
        ]);
    }

    public function isSingleValueType(?string $type): bool
    {
        return in_array($type, [
            Configuration::SELECT,
            Configuration::WEEKDAY,
            Configuration::BOOLEAN,
            Configuration::IMAGE,
            Configuration::FILE,
        ]);
    }

    public function isObjectValueType(?string $type): bool
    {
        return in_array($type, [
            Configuration::RANGE,
            Configuration::IMG_BUTTON,
        ]);
    }

    public function isArrayValueType(?string $type): bool
    {
        return in_array($type, [
            Configuration::MULTI_SELECT,
        ]);
    }

    public function canStoreFiles(string $type): bool
    {
        return in_array($type, [
            Configuration::IMAGE,
            Configuration::FILE,
            Configuration::IMG_BUTTON,
        ]);
    }

    public function getRuleForType($type, $attribute): array
    {
        if (!in_array($type, Configuration::$types)) {
            return [];
        }

        if (Configuration::isArrayValueType($type)) {
            return [
                $attribute => 'nullable|array',
                $attribute . '*' => 'required|string',
            ];
        }

        if (Configuration::TEXT === $type) {
            return [
                $attribute => 'nullable|string|max:500',
            ];
        }

        if (in_array($type, [Configuration::TEXTAREA, Configuration::EDITOR])) {
            return [
                $attribute => 'nullable|string|max:5000',
            ];
        }

        if (Configuration::WEEKDAY === $type) {
            return [
                $attribute => 'required|string|max:20',
            ];
        }

        if (Configuration::TIME === $type) {
            return [
                $attribute => 'nullable|string|max:10',
            ];
        }

        if (Configuration::DATE === $type) {
            return [
                $attribute => 'nullable|date',
            ];
        }

        if (Configuration::BOOLEAN === $type) {
            return [
                $attribute => 'boolean',
            ];
        }

        if (Configuration::SELECT === $type) {
            return [
                $attribute => 'nullable|string',
            ];
        }

        if (Configuration::IMAGE === $type) {
            return [
                $attribute . '.image' => 'nullable|image',
                $attribute . '.isRemoveImage' => 'nullable|boolean',
            ];
        }

        if (Configuration::FILE === $type) {
            return [
                $attribute . '.file' => 'nullable|file',
                $attribute . '.isRemoveImage' => 'nullable|boolean',
            ];
        }

        if (Configuration::IMG_BUTTON === $type) {
            return [
                $attribute . '.image' => 'nullable|image',
                $attribute . '.isRemoveImage' => 'nullable|boolean',
                $attribute . '.url' => 'nullable|string|max:250',
                $attribute . '.title' => 'nullable|string|max:100',
                $attribute . '.content' => 'nullable|string|max:5000',
            ];
        }

        if (Configuration::RANGE === $type) {
            return [
                $attribute . '.from' => 'nullable|string',
                $attribute . '.to' => 'nullable|string',
            ];
        }

        return [];
    }

    public static function getValueByKey(string $key)
    {
        $configuration = AdminConfiguration::where('key', $key)->first();

        if ($configuration) {
            return $configuration->value;
        }

        $configValue = config('hexide-admin.variables.' . $key);

        if (!$configValue) {
            return null;
        }

        $configValue['key'] = $key;

        $data = Arr::only($configValue, ['key', 'name', 'type']);

        $data = isset($configValue['localization'])
            ? array_merge($data, ['translatable' => true], $configValue['localization'])
            : array_merge($data, ['value' => $configValue['plain_value']]);

        $configuration = AdminConfiguration::create($data);

        return $configuration->value;
    }


    public function storeToCache(): array
    {
        $collection = AdminConfiguration::visible()->sorted()
            ->joinTranslations()
            ->with('translations')
            ->select([
                'admin_configurations.*',
                'admin_configuration_translations.text as text',
            ])
            ->get()
            ->groupBy('group');

        $data = [];

        /** @var AdminConfiguration $admin_configuration */
        foreach ($collection as $groupName => $admin_configurations) {
            $values = [];

            foreach ($admin_configurations as $admin_configuration) {
                $values[$admin_configuration->key] = $admin_configuration->value;
            }

            $data[$groupName] = $values;
        }

        Cache::forever('configurations_' . app()->getLocale(), $data);

        return $data;
    }

    public function readFromCache(): ?array
    {
        return Cache::get('configurations_' . app()->getLocale(), $this->storeToCache());
    }

    public function lists(): array
    {
        return $this->readFromCache();
    }

    /**
     * @param string|array $group
     *
     * @return array
     */
    public function configurations($group = []): array
    {
        return collect($this->readFromCache())
            ->only(array_wrap($group))
            ->toArray();
    }
}
