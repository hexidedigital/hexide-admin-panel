<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Classes;

use File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Image;
use Intervention\Image\Constraint;
use Intervention\Image\Image as ImageClass;

class Thumb
{
    /** @var ImageClass|Image|null */
    protected $image = null;
    protected ?string $cachedImgPath = null;
    protected ?string $oldPath = null;
    protected ?string $postfix = null;

    /** Create table method */
    public function create(string $path): Thumb
    {
        $path = $this->wrapPublicPath($this->clearPath($path));

        $ins = new static;

        if (File::exists($path)) {
            $ins->oldPath = $path;
            $ins->image = Image::make($path);
        }

        return $ins;
    }

    public function getFormat()
    {
        return config('hexide-admin.thumbnails.format', 'webp');
    }

    public function getQuality()
    {
        return config('hexide-admin.thumbnails.quality', 90);
    }

    public function getThumbFolder(): Stringable
    {
        return Str::of(config('hexide-admin.thumbnails.quality', 'storage/thumbs'))
            ->finish('/');
    }

    public function encodeImage()
    {
        $shouldEncode = config('hexide-admin.thumbnails.encode_on_adapt', true);

        if ($shouldEncode) {
            return $this->image->encode($this->getFormat(), $this->getQuality());
        }

        return $this->image;
    }

    public function makePostfix($width, $height): string
    {
        return '_' . $width . 'x' . $height;
    }

    /** Quick method to create a square thumb image */
    public function square(string $path, int $size): Thumb
    {
        return $this->thumb($path, $size, $size);
    }

    /** Quick method to resize image with aspect ratio and upsize */
    public function thumb(string $path, ?int $width = null, ?int $height = null): Thumb
    {
        $path = $this->clearPath($path);

        if ($this->cached($path, $width, $height)) {
            return $this;
        }

        return $this->create($path)->resize($width, $height);
    }

    /** Resize image with aspect ratio and upsize */
    public function resize(int $width = null, int $height = null): Thumb
    {
        if ($this->image) {
            $this->encodeImage()->resize($width, $height, function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $this->postfix = $this->makePostfix($width, $height);
        }

        return $this;
    }

    /** Quick method to resize image with aspect ratio */
    public function thumb_adapt(string $path, ?int $width = null, ?int $height = null): Thumb
    {
        $path = $this->clearPath($path);

        if ($this->cached($path, $width, $height)) {
            return $this;
        }

        return $this->create($path)->adapt($width, $height);
    }

    /** Resize image with aspect ratio */
    public function adapt(int $width = null, int $height = null): Thumb
    {
        if ($this->image) {
            $this->encodeImage()->resize($width, $height, function (Constraint $constraint) {
                $constraint->aspectRatio();
            });

            $this->postfix = $this->makePostfix($width, $height);
        }

        return $this;
    }

    /**
     * Return link to modified image of false if an error has occurred
     *
     * @return bool|string|null
     */
    public function link()
    {
        if ($this->cachedImgPath) {
            return $this->cachedImgPath;
        }

        if ($this->image) {
            $file = $this->getNewFilePath();

            if (File::exists(public_path($file))) {
                return $file;
            }

            return $this->save($file);
        }

        return null;
    }

    /**
     * Save modified image
     *
     * @param string|null $path
     *
     * @return bool|string
     */
    public function save(string $path = null)
    {
        if ($this->image) {
            $path = $path ?: $this->getNewFilePath();

            if ($this->image->save($this->wrapPublicPath($path))) {
                return $path;
            }
        }

        return false;
    }

    /** Get cached path for image if it exists */
    public function cached(string $path, int $width = null, int $height = null): bool
    {
        $path = $this->wrapPublicPath($path);

        $path = $this->getThumbFolder()
            . $this->nestedPath($path) . '/'
            . md5($path)
            . $this->makePostfix($width, $height) . '.'
            . $this->getFormat();

        if (file_exists(public_path($path))) {
            $this->cachedImgPath = url($path);

            return true;
        }

        return false;
    }

    /** Replace app url and replace `//` on `/` */
    public function clearPath(string $path): string
    {
        $path = str_replace(config('app.url'), '/', $path);
        $path = str_replace('//', '/', $path);

        return $path;
    }

    /** If the given path is not a public path, wrap it in one */
    public function wrapPublicPath(string $path): string
    {
        if (strpos($path, public_path()) === false) {
            $path = public_path($path);
        }

        return $path;
    }

    /** Generate nested directory path based on name and append name hash for file */
    public function nestedPath(string $name): string
    {
        $hash = md5($name);
        $a = substr($hash, 0, 2);
        $b = substr($hash, 2, 2);

        return implode('/', [$a, $b]);
    }

    /** return new file location on disk */
    private function getNewFilePath(): ?string
    {
        if ($this->image) {
            $hash = md5($this->oldPath);

            $path = $this->getThumbFolder() . $this->nestedPath($this->oldPath);

            if (!File::exists(public_path($path))) {
                File::makeDirectory(public_path($path), 0755, true);
            }

            return "$path/" . $hash . $this->postfix . '.' . $this->getFormat();
        }

        return null;
    }
}
