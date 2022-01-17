<?php

namespace HexideDigital\HexideAdmin\Classes\DBTranslations;

use App\Models\Translation;
use Exception;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Log;

class DBLoader implements Loader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): ?array
    {
        try {
            $group = $this->_getGroup($group);

            if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                $result = cache()->tags('translations')->get($locale . '_' . $group, null);
            } else {
                $result = Cache::get('translations_' . $locale . '_' . $group, null);
            }

            if ($result === null) {
                $result = Translation::whereLocale($locale)
                    ->whereGroup($group)
                    ->whereNotNull('value')
                    ->where('value', '<>', '')
                    ->get(['key', 'value'])
                    ->pluck('value', 'key')
                    ->toArray();

                if (App::isProduction() && Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                    cache()->tags('translations')->put($locale . '_' . $group, $result, 60);
                }

                Cache::put('translations_' . $locale . '_' . $group, $result, 60);
            }

            return $result;
        } catch (Exception $e) {
            // just insure themselves in case of problems with the database
            Log::critical(
                'message: ' . $e->getMessage() . ', line: ' . $e->getLine() . ', file: ' . $e->getFile(),
                [
                    'locale' => $locale,
                    'group' => $group,
                ]
            );

            return [];
        }
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        //
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param string $path
     *
     * @return void
     */
    public function addJsonPath($path)
    {
        //
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces()
    {
        //
    }

    /**
     * @param string $group
     *
     * @return string
     */
    private function _getGroup(string $group): string
    {
        if (request()->routeIs('admin')) {
            return $group;
        }

        if ($group !== 'validation') {
            return $group;
        }

        return 'front_validation';
    }
}
