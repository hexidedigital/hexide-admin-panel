<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Classes;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploader
{
    private string $disk = 'public';
    private $storage;

    public function __construct()
    {
        $this->storage = Storage::disk($this->disk);
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;
        $this->storage = Storage::disk($this->disk);

        return $this;
    }

    public function getDiskName(): string
    {
        return $this->disk;
    }

    /**
     * Get url for specified driver
     *
     * See url configs for each driver in filesystem.php
     */
    public function url(?string $path): string
    {
        if (empty($path)) return '';
        if (Str::startsWith($path, 'http')) return $path;

        $path = $this->_clearPath($path);

        return $this->storage->url($path);
    }

    /** Get full path from system root */
    public function path(?string $path): string
    {
        if (empty($path)) return '';
        if (Str::startsWith($path, 'http')) return $path;

        $path = $this->_clearPath($path);

        return $this->storage->path($path);
    }

    /**
     * Put file into nested path
     *
     * @param UploadedFile|File|null $file
     * @param string $type directory that will contain the files
     * @param string|null $module name of module for separate files if type directory
     * @param string|null $uniq_id for generating same nested path
     *
     * @return string|null
     */
    public function put($file, string $type, ?string $module = null, ?string $uniq_id = null): ?string
    {
        if (empty($file)) {
            return null;
        }

        $module = empty($module) ? '' : '/' . $module;

        $path = $this->_preparePath($type . $module, $uniq_id);

        return $this->storage->putFile($path, new File($file->getPathname())) ?: null;
    }

    /**
     * Preparing of nested folders based on uniq key or random key
     *
     * @param string $root
     * @param string|null $uniq_id
     * @return string
     */
    public function _preparePath(string $root, ?string $uniq_id = null): string
    {
        $hash = md5($uniq_id ?? Str::random());

        $a = substr($hash, 0, 2);
        $b = substr($hash, 2, 2);

        return $root . '/' . $a . '/' . $b;
    }

    /** Remove storage prepend string from path */
    public function _clearPath(?string $path): string
    {
        return str_replace('storage/', '', $path ?: '');
    }

    /**
     * @param $name
     * @param array $arguments
     * @return $this
     */
    public function __call($name, array $arguments)
    {
        return new static();
    }
}
