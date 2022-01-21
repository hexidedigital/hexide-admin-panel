<?php

namespace HexideDigital\HexideAdmin\Classes\DBTranslations;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Translation\FileLoader;

class MixedLoader implements Loader
{
    protected DBLoader $databaseLoader;
    protected FileLoader $fileLoader;

    /** All the namespace hints. */
    protected array $hints = [];

    /** Create a new mixed loader instance. */
    public function __construct(DBLoader $databaseLoader, FileLoader $fileLoader)
    {
        $this->databaseLoader = $databaseLoader;
        $this->fileLoader = $fileLoader;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param  string $locale
     * @param  string $group
     * @param  string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        return array_replace_recursive(
            $this->fileLoader->load($locale, $group, $namespace),
            $this->databaseLoader->load($locale, $group, $namespace)
        );
    }

    /**
     *  Add a new namespace to the loader.
     *
     * @param  string $namespace
     * @param  string $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;
        $this->fileLoader->addNamespace($namespace, $hint);
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
        $this->fileLoader->addJsonPath($path);
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces(): array
    {
        return $this->fileLoader->namespaces();
    }
}
