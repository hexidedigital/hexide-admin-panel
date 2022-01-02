<?php

namespace HexideDigital\HexideAdmin\Classes\DBTranslations;

use App\Models\Translation;
use Exception;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Log;
use function app;
use function cache;
use function request;

/**
 * Class DBLoader
 * @package App\Classes
 */
class DBLoader implements Loader
{

    /**
     * Load the messages for the given locale.
     *
     * @param  string $locale
     * @param  string $group
     * @param  string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): ?array
    {
        try {
            $group = $this->_getGroup($group);

            $result = null;

            if(Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                $result = cache()->tags('translations')->get($locale.'_'.$group, null);
            }

            if ($result === null) {
                $result = Translation::whereLocale($locale)
                    ->whereGroup($group)
                    ->whereNotNull('value')
                    ->where('value', '<>', '')
                    ->get(['key', 'value'])
                    ->keyBy('key')
                    ->map(
                        function ($item) {
                            return $item['value'];
                        }
                    )
                    ->toArray();

                foreach ($result as $key => $value) {
                    unset($result[$key]);

                    Arr::set($result, $key, $value);
                }

                if (app()->environment('production') && Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                    cache()->tags('translations')->put($locale.'_'.$group, $result, 60);
                }
            }

            return $result;
        } catch (Exception $e) {
            // just insure themselves in case of problems with the database
            Log::critical(
                'message: '.$e->getMessage().', line: '.$e->getLine().', file: '.$e->getFile(),
                [
                    'locale' => $locale,
                    'group'  => $group,
                ]
            );

            return [];
        }
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string $namespace
     * @param  string $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        // TODO: Implement addNamespace() method.
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param  string $path
     *
     * @return void
     */
    public function addJsonPath($path)
    {
        // TODO: Implement addJsonPath() method.
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces()
    {
        // TODO: Implement namespaces() method.
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
