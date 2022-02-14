<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Classes;

use File;
use Image;
use Intervention\Image\Image as ImageClass;

class Thumb
{
    /** @var ImageClass|Image|null */
    private $img = null;
    private ?string $cachedImg = null;
    private ?string $oldPath = null;
    private ?string $postfix = null;

    /** Create table method */
    public function create(string $path): Thumb
    {
        $path = $this->wrapPublicPath($this->clearPath($path));

        $ins = new static;

        if (File::exists($path)) {
            $ins->oldPath = $path;
            $img = Image::make($path);
            $ins->img = $img;
        }

        return $ins;
    }

    /** Quick method to create a square thumb image */
    public function square(string $path, int $s): Thumb
    {
        return $this->thumb($path, $s, $s);
    }

    /** Quick method to resize image with aspect ratio and upsize */
    public function thumb(string $path, int $w = null, int $h = null): Thumb
    {
        $path = $this->clearPath($path);

        if ($this->cached($path, $w, $h)) {
            return $this;
        }

        $int = self::create($path);

        return $int->resize($w, $h);
    }

    /** Quick method to resize image with aspect ratio */
    public function thumb_adapt(string $path, int $w = null, int $h = null): Thumb
    {
        $path = $this->clearPath($path);

        if ($this->cached($path, $w, $h)) {
            return $this;
        }

        $int = self::create($path);

        return $int->adapt($w, $h);
    }

    /** Resize image with aspect ratio */
    public function adapt(int $w = null, int $h = null): Thumb
    {
        if ($this->img) {
            $this->img->resize($w, $h, function ($constraint) {
                $constraint->aspectRatio();
            });
            $this->postfix = "_{$w}x{$h}";
        }

        return $this;
    }

    /** Resize image with aspect ratio and upsize */
    public function resize(int $w = null, int $h = null): Thumb
    {
        if ($this->img) {
            $this->img->resize($w, $h, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $this->postfix = "_{$w}x{$h}";
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
        if ($this->cachedImg) return $this->cachedImg;

        if ($this->img) {
            $file = $this->getNewFilePath();

            if (File::exists(public_path($file))) {
                return $file;
            } else {
                return $this->save($file);
            }
        }

        return null;
    }

    /**
     * Save modified image
     *
     * @param string|null $path
     * @return bool|string
     */
    public function save(string $path = null)
    {
        if ($this->img) {
            $path = $path ?: $this->getNewFilePath();

            if ($this->img->save($path)) {
                return $path;
            }
        }

        return false;
    }

    /** Get cached path for image if it exists */
    public function cached(string $path, int $w = null, int $h = null): bool
    {
        $path = $this->wrapPublicPath($path);

        $path = 'thumbs/' . $this->nestedPath($path) . '/' . $this->hash($path) . "_{$w}x{$h}" . '.' . pathinfo($path, PATHINFO_EXTENSION);

        if (file_exists(public_path($path))) {
            $this->cachedImg = url($path);

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
        $hash = $this->hash($name);
        $a = substr($hash, 0, 2);
        $b = substr($hash, 2, 2);

        return implode('/', [$a, $b]);
    }

    public function hash(string $name): string
    {
        return md5($name);
    }

    /** return new file location on disk */
    private function getNewFilePath(): ?string
    {
        if ($this->img) {
            $hash = $this->hash($this->oldPath);

            $path = 'thumbs/' . $this->nestedPath($this->oldPath);

            if (!File::exists(public_path($path))) {
                File::makeDirectory(public_path($path), 0755, true);
            }

            return $path . '/' . $hash . $this->postfix . '.' . pathinfo($this->oldPath, PATHINFO_EXTENSION);
        }

        return null;
    }
}
