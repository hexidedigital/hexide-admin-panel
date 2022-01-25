<?php

namespace HexideDigital\HexideAdmin\Services\Backend;

use HexideDigital\HexideAdmin\Models\Translation;
use Exception;
use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TranslationsService extends BackendService
{
    private ?string $group;

    public function __construct(?string $group)
    {
        parent::__construct();

        $this->group = $group;
        $this->locales = $this->group == 'admin_labels' ? config('hexide-admin.locales') : config('translatable.locales');
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    public function getLocales()
    {
        return $this->locales;
    }

    public function setLocales(array $locales): void
    {
        $this->locales = $locales;
    }

    /**
     * @param FormRequest|Request $request
     *
     * @return void
     * @throws Exception
     */
    public function updateTranslations(Request $request)
    {
        $inputs = collect($request->validated());

        foreach ($this->locales as $locale) {
            $translations = $this->getGroupTranslationsForLocale($locale)->merge($inputs->get($locale));

            foreach ($translations as $key => $value) {
                Translation::updateOrCreate([
                    'locale' => $locale,
                    'group' => $this->group,
                    'key' => $key,
                ], [
                    'value' => $value,
                ]);
            }

//            if (Cache::getStore() instanceof TaggableStore) {
//                cache()->tags('translations')->forget($locale . '_' . $this->group);
//            }
        }
    }

    public function getPaginatedList(int $perPage = 15, int $page = 1, array $options = []): LengthAwarePaginator
    {
        $list = $this->getGroupTranslations();

        return new LengthAwarePaginator(
            $list->slice(($page - 1) * $perPage, $perPage),
            $list->count(),
            $perPage,
            $page,
            $options
        );
    }

    /**
     * Get all translations for keys for current group <br>
     * Merge translations from database and from local file <br>
     * collection are sorted by key
     */
    public function getGroupTranslations(): Collection
    {
        $list = [];

        foreach ($this->locales as $locale) {
            $list = $this->getTranslationsForLocale($locale, $list);
        }

        foreach ($list as $key => $item) {
            $item['key'] = $key;
            $list[$key] = collect($item);
        }

        return collect($list)->sortKeys();
    }

    /**
     * Return list of translations with key-value mode <br>
     * every value is array, where key is locale and value form database or file
     */
    public function getTranslationsForLocale(string $locale, array $list): array
    {
        $databaseTranslations = Translation::whereLocale($locale)
            ->whereGroup($this->getGroup())
            ->pluck('value', 'key');

        /*
         * Get array with keys from file
         * Values are at first gets from database, if is not existed, gets from local file
         */
        $fileArray = collect(Arr::dot($this->getArrayFromFile($locale)));
        foreach ($fileArray as $key => $value) {
            $list[$key][$locale] = collect([
                'value' => $databaseTranslations->get($key, $value),
                'value_from_db' => $databaseTranslations->has($key),
                'exists_in_file' => true,
                'is_same' => $databaseTranslations->get($key) === $value,
            ]);
        }

        /*
         * Append to list missed in current locale file translations from database table
         */
        foreach ($databaseTranslations->except($fileArray->keys()) as $key => $value) {
            $list[$key][$locale] = collect([
                'value' => $value,
                'value_from_db' => true,
                'exists_in_file' => false,
                'is_same' => false,
            ]);
        }

        return $list;
    }

    /**
     * Return value only for needed locale
     *
     * @param string $locale
     *
     * @return Collection<string, string|null> array map -- key-value
     */
    public function getGroupTranslationsForLocale(string $locale): Collection
    {
        $list = collect();

        foreach ($this->getGroupTranslations() as $key => $translations) {
            $list->put($key, $translations->get($locale));
        }

        return $list;
    }

    /**
     * @param string $locale
     *
     * @return array<string, array|string>
     */
    protected function getArrayFromFile(string $locale): array
    {
        $path = lang_path('/' . $locale . '/' . $this->getGroup() . '.php');

        return file_exists($path) ? include($path) : [];
    }

    protected function getArrayFromJsonFile(string $locale)
    {
        $path = lang_path('/' . $locale . '.json');

        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }
}
