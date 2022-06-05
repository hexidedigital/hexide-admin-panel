<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Services\Backend;

use Exception;
use GuzzleHttp\Utils;
use HexideDigital\HexideAdmin\Classes\DBTranslations\TranslationItem;
use HexideDigital\HexideAdmin\Classes\DBTranslations\TranslationRow;
use HexideDigital\HexideAdmin\Models\Translation;
use HexideDigital\HexideAdmin\Services\BackendService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class TranslationsService extends BackendService
{
    private ?string $group;

    public function __construct(?string $group)
    {
        parent::__construct();

        $this->group = $group;
        $this->locales = $this->useAdminLocales($group)
            ? config('hexide-admin.locales')
            : config('translatable.locales');
    }

    public function useAdminLocales(?string $group): bool
    {
        return $group == 'admin_labels';
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
        $inputs = $request->safe()->collect();

        foreach ($this->locales as $locale) {
            $translations = collect($inputs->get($locale));

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

    /**
     * Get all translations for keys for current group <br>
     * Merge translations from database and from local file <br>
     * collection are sorted by key
     */
    public function getGroupTranslations(): Collection
    {
        $accumulatedTranslations = collect();
        foreach ($this->locales as $locale) {
            foreach ($this->getTranslationsForLocale($locale) as $item) {
                $key = $item->getKey();

                /** @var TranslationRow $row */
                $row = $accumulatedTranslations->pull($key, TranslationRow::touch($key));
                $row->addTranslation($item);
                $accumulatedTranslations->put($key, $row);
            }
        }

        return $accumulatedTranslations->sortKeys();
    }

    /**
     * Return list of translations with key-value mode <br>
     * every value is array, where key is locale and value form database or file
     *
     * @param string $locale
     * @return \Generator<TranslationItem>|TranslationItem[]
     */
    public function getTranslationsForLocale(string $locale): \Generator
    {
        $databaseTranslations = Translation::whereLocale($locale)
            ->whereGroup($this->getGroup())
            ->pluck('value', 'key');

        /*
         * Get array with keys from file
         * Values are at first gets from database, if is not existed, gets from local file
         */
        $fileArray = collect(Arr::dot($this->getArrayFromFile($locale)))
            ->filter(fn($item) => is_null($item) || is_string($item));

        foreach ($fileArray as $key => $fileValue) {
            yield TranslationItem::touch($locale)
                ->setValue($databaseTranslations->get($key, $fileValue))
                ->setKey($key)
                ->setIsSame($databaseTranslations->get($key) === $fileValue)
                ->setIsValueExistsInFile(true)
                ->setIsValueFromDatabase($databaseTranslations->has($key));
        }

        /*
         * Append to list missed in current locale file translations from database table
         */
        foreach ($databaseTranslations->except($fileArray->keys()) as $key => $value) {
            yield TranslationItem::touch($locale)
                ->setValue($value)
                ->setKey($key)
                ->setIsSame(false)
                ->setIsValueExistsInFile(false)
                ->setIsValueFromDatabase(true);
        }
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

        return File::exists($path) ? include($path) : [];
    }

    protected function getArrayFromJsonFile(string $locale)
    {
        $path = lang_path('/' . $locale . '.json');

        return File::exists($path) ? Utils::jsonDecode(File::get($path), true) : [];
    }
}
