<?php

namespace HexideDigital\HexideAdmin\Models;

use Astrotomic\Translatable\Translatable;
use HexideDigital\HexideAdmin\Contracts\WithTypesContract;
use HexideDigital\HexideAdmin\Models\Traits\VisibleTrait;
use HexideDigital\HexideAdmin\Models\Traits\WithTranslationsTrait;
use HexideDigital\HexideAdmin\Models\Traits\WithTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @mixin AdminConfigurationTranslation
 * @mixin \Eloquent
 *
 * @property int $id
 * @property string $key
 * @property string $type
 * @property string|null $name
 * @property string|null $description
 * @property int $translatable
 * @property string|null $group
 * @property int $in_group_position
 * @property string|null $content
 * @property string|array|null $value
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AdminConfigurationTranslation|null $translation
 * @property-read Collection|AdminConfigurationTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static Builder|static joinTranslations(?string $modelTable = null, ?string $translationsTable = null, ?string $modelTableKey = null, ?string $translationsTableKey = null)
 * @method static Builder|static listsTranslations(string $translationField)
 * @method static Builder|static newModelQuery()
 * @method static Builder|static newQuery()
 * @method static Builder|static notTranslatedIn(?string $locale = null)
 * @method static Builder|static orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|static query()
 * @method static Builder|static sorted(string $direction = 'ASC')
 * @method static Builder|static sortedAsc()
 * @method static Builder|static sortedDesc()
 * @method static Builder|static translated()
 * @method static Builder|static translatedIn(?string $locale = null)
 * @method static Builder|static visible()
 * @method static Builder|static forGroup($group)
 * @method static Builder|static whereCreatedAt($value)
 * @method static Builder|static whereDescription($value)
 * @method static Builder|static whereGroup($value)
 * @method static Builder|static whereId($value)
 * @method static Builder|static whereInGroupPosition($value)
 * @method static Builder|static whereKey($value)
 * @method static Builder|static whereName($value)
 * @method static Builder|static whereStatus($value)
 * @method static Builder|static whereTranslatable($value)
 * @method static Builder|static whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|static whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static whereType($value)
 * @method static Builder|static whereUpdatedAt($value)
 * @method static Builder|static whereValue($value)
 * @method static Builder|static withTranslation()
 * @method static Builder|static withTranslations()
 */
class AdminConfiguration extends Model implements WithTypesContract
{
    use Translatable, WithTranslationsTrait;
    use VisibleTrait;
    use WithTypes;

    public const DefaultType = self::TEXT;

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
        self::TEXT,
        self::TEXTAREA,
        self::EDITOR,
        self::WEEKDAY,
        self::TIME,
        self::DATE,
        self::BOOLEAN,
        self::SELECT,
        self::MULTI_SELECT,
        self::IMAGE,
        self::FILE,
        self::RANGE,
        self::IMG_BUTTON,
    ];

    /* ------------------------ Model ------------------------ */

    public $translationModel = AdminConfigurationTranslation::class;

    protected array $translatedAttributes = ['text', 'json',];

    protected $fillable = [
        'key', 'type', 'name', 'description', 'translatable', 'content', 'value', 'status', 'group', 'in_group_position',
    ];

    protected $casts = [
        'status'       => 'bool',
        'translatable' => 'bool',
        'value'        => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (AdminConfiguration $adminConfiguration) {
            $adminConfiguration->key = $adminConfiguration->attributes['key'];
        });

        static::updating(function (AdminConfiguration $adminConfiguration) {
            $adminConfiguration->key = $adminConfiguration->attributes['key'];
        });

        static::deleted(function (AdminConfiguration $adminConfiguration) {

        });
    }

    /* ------------------------ attributes ------------------------ */

    public function setKeyAttribute(string $value)
    {
        $this->attributes['key'] = Str::slug($value, '_');
    }

    public function setValueAttribute($value)
    {
        $field = $this->storeKey();

        if (self::isSingleValueType($this->type)) {

            $value = $this->asJson($value);

        } else if (self::isArrayValueType($this->type)) {

            if (empty($value)) {
                $value = [];
            }

            $value = $this->asJson($value);

        } else if (self::isObjectValueType($this->type)) {

            $value = array_wrap($value);

            if (self::isType(self::RANGE)) {

                $value = $this->asJson([
                    'from' => array_get($value, 'from'),
                    'to'   => array_get($value, 'to'),
                ]);

            } else if (self::isType(self::IMG_BUTTON)) {

                $value = $this->asJson([
                    'image'   => array_get($value, 'image'),
                    'url'     => array_get($value, 'url'),
                    'title'   => array_get($value, 'title'),
                    'content' => array_get($value, 'content'),
                ]);

            }

        }

        $this->attributes[$field] = $value;
    }

    public function getValueAttribute($value)
    {
        $field = $this->storeKey();

        $value = $this->translatable ? ($this->translate()->attributes[$field] ?? null) : $this->attributes[$field];

        if (self::isTextValueType($this->type)) {
            return $value;
        }

        if (self::isSingleValueType($this->type)) {
            return $this->fromJson($value);
        }

        if (self::isArrayValueType($this->type)) {
            if (empty($value)) {
                return [];
            }

            return $this->fromJson($value);
        }

        if (self::isObjectValueType($this->type)) {
            return $this->fromJson($value);
        }

        return $value;
    }

    /* ------------------------ scopes ------------------------ */

    /**
     * @param Builder $builder
     * @param string|array<string> $groups
     *
     * @return Builder
     */
    public function scopeForGroup(Builder $builder, $groups): Builder
    {
        return $builder->whereIn('group', array_wrap($groups));
    }

    public function scopeSorted(Builder $builder, string $direction = 'ASC'): Builder
    {
        return $builder->orderBy('in_group_position', $direction);
    }

    public function scopeSortedAsc(Builder $builder): Builder
    {
        return $builder->orderBy('in_group_position', 'ASC');
    }

    public function scopeSortedDesc(Builder $builder): Builder
    {
        return $builder->orderBy('in_group_position', 'DESC');
    }

    /* ------------------------ operations ------------------------ */

    /**
     * @param string|array $group
     *
     * @return array
     */
    public static function varGroups($group = []): array
    {
        $collection = AdminConfiguration::visible()
            ->joinTranslations()
            ->select([
                'admin_configurations.*',
                'admin_configuration_translations.text as text',
            ])
            ->forGroup($group)
            ->sorted()
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

        return $data;
    }

    public static function getValueByKey(string $key)
    {
        $configuration = self::where('key', $key)->first();

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

        $configuration = self::create($data);

        return $configuration->value;
    }

    public function isType(string $type): bool
    {
        return in_array($type, self::$types) && $this->type === $type;
    }

    public function storeKey(): string
    {
        return self::getStoreKey($this->type, $this->translatable);
    }

    /* ------------------------ static ------------------------ */

    public static function getStoreKey(string $type, ?bool $translatable): string
    {
        if (self::isTextValueType($type)) {
            return $translatable ? 'text' : 'content';
        }

        return $translatable ? 'json' : 'value';
    }

    public static function isTextValueType(?string $type): bool
    {
        return in_array($type, [
            self::TEXT,
            self::TEXTAREA,
            self::EDITOR,
            self::TIME,
            self::DATE,
        ]);
    }

    public static function isSingleValueType(?string $type): bool
    {
        return in_array($type, [
            self::SELECT,
            self::WEEKDAY,
            self::BOOLEAN,
            self::IMAGE,
            self::FILE,
        ]);
    }

    public static function isObjectValueType(?string $type): bool
    {
        return in_array($type, [
            self::RANGE,
            self::IMG_BUTTON,
        ]);
    }

    public static function isArrayValueType(?string $type): bool
    {
        return in_array($type, [
            self::MULTI_SELECT,
        ]);
    }

    public static function canStoreFiles(string $type): bool
    {
        return in_array($type, [
            self::IMAGE,
            self::FILE,
            self::IMG_BUTTON,
        ]);
    }

    public static function getRuleForType($type, $attribute): array
    {
        if (!in_array($type, AdminConfiguration::getTypes())) {
            return [];
        }

        if (AdminConfiguration::isArrayValueType($type)) {
            return [
                $attribute       => 'nullable|array',
                $attribute . '*' => 'required|string',
            ];
        }

        if (AdminConfiguration::TEXT === $type) {
            return [
                $attribute => 'nullable|string|max:500',
            ];
        }

        if (in_array($type, [AdminConfiguration::TEXTAREA, AdminConfiguration::EDITOR])) {
            return [
                $attribute => 'nullable|string|max:5000',
            ];
        }

        if (AdminConfiguration::WEEKDAY === $type) {
            return [
                $attribute => 'required|string|max:20',
            ];
        }

        if (AdminConfiguration::TIME === $type) {
            return [
                $attribute => 'nullable|string|max:10',
            ];
        }

        if (AdminConfiguration::DATE === $type) {
            return [
                $attribute => 'nullable|date',
            ];
        }

        if (AdminConfiguration::BOOLEAN === $type) {
            return [
                $attribute => 'boolean',
            ];
        }

        if (AdminConfiguration::SELECT === $type) {
            return [
                $attribute => 'nullable|string',
            ];
        }

        if (AdminConfiguration::IMAGE === $type) {
            return [
                $attribute . '.image'         => 'nullable|image',
                $attribute . '.isRemoveImage' => 'nullable|boolean',
            ];
        }

        if (AdminConfiguration::FILE === $type) {
            return [
                $attribute . '.file'          => 'nullable|file',
                $attribute . '.isRemoveImage' => 'nullable|boolean',
            ];
        }

        if (AdminConfiguration::IMG_BUTTON === $type) {
            return [
                $attribute . '.image'         => 'nullable|image',
                $attribute . '.isRemoveImage' => 'nullable|boolean',
                $attribute . '.url'           => 'nullable|string|max:250',
                $attribute . '.title'         => 'nullable|string|max:100',
                $attribute . '.content'       => 'nullable|string|max:5000',
            ];
        }

        if (AdminConfiguration::RANGE === $type) {
            return [
                $attribute . '.from' => 'nullable|string',
                $attribute . '.to'   => 'nullable|string',
            ];
        }

        return [];
    }

}
