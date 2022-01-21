<?php

namespace HexideDigital\HexideAdmin\Services\Backend;

use HexideDigital\HexideAdmin\Models\Translation;
use Exception;
use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Cache\TaggableStore;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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
        $inputs = $request->validated();

        foreach ($this->locales as $locale) {
            $translations = array_merge(
                $this->getGroupTranslationsForLocale($locale),
                Arr::get($inputs, $locale, [])
            );

            foreach ($translations as $key => $value) {
                Translation::updateOrCreate([
                    'locale' => $locale,
                    'group' => $this->group,
                    'key' => $key,
                ], [
                    'value' => $value,
                ]);
            }

            if (Cache::getStore() instanceof TaggableStore) {
                cache()->tags('translations')->forget($locale . '_' . $this->group);
            }
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

    public function getListOfTranslations(): Collection
    {
        return $this->getGroupTranslations();
    }

    /**
     * Get all translations for keys for current group <br>
     * Merge translations from database and from local file <br>
     * collection are sorted by key
     */
    private function getGroupTranslations(): Collection
    {
        $list = [];

        foreach ($this->locales as $locale) {
            $list = $this->getTranslationsForLocale($list, $locale);
        }

        ksort($list);

        return Collection::make($list);
    }

    /**
     * Return list of translations with key-value mode <br>
     * every value is array, where key is locale and value form database or file
     */
    private function getTranslationsForLocale(array $list, string $locale): array
    {
        $databaseTranslations = Translation::whereLocale($locale)
            ->whereGroup($this->getGroup())
            ->pluck('value', 'key');

        /*
         * Get array with keys from file
         * Values are at first gets from database, if is not existed, gets from local file
         */
        $fileArray = Arr::dot($this->getArrayFromFile($locale));
        foreach ($fileArray as $key => $value) {
            $list[$key][$locale] = Arr::get($databaseTranslations, $key, $value);
        }

        /*
         * Append to list missed in current locale file translations from database table
         */
        foreach (Arr::except($databaseTranslations->toArray(), array_keys($fileArray)) as $key => $value) {
            $list[$key][$locale] = $value;
        }

        return $list;
    }

    /**
     * Return value only for needed locale
     *
     * @param string $locale
     *
     * @return array<string, string|null> array map -- key-value
     */
    private function getGroupTranslationsForLocale(string $locale): array
    {
        $list = [];

        foreach ($this->getGroupTranslations() as $key => $translations) {
            $list[$key] = Arr::get($translations, $locale);
        }

        return $list;
    }

    /**
     * @param string $locale
     *
     * @return array<string, array|string>
     */
    private function getArrayFromFile(string $locale): array
    {
        $path = lang_path('/' . $locale . '/' . $this->getGroup() . '.php');

        return file_exists($path) ? include($path) : [];
    }

    private function getArrayFromJsonFile(string $locale)
    {
        $path = lang_path('/' . $locale . '.json');

        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }
}
