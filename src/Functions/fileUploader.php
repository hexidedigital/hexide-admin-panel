<?php

declare(strict_types=1);

if (!function_exists('file_uploader')) {
    /** Get the FileUploader instance */
    function file_uploader(): \HexideDigital\HexideAdmin\Classes\FileUploader
    {
        return app(\HexideDigital\HexideAdmin\Classes\FileUploader::class);
    }
}

if (!function_exists('fu_disk')) {
    /** Get the FileUploader instance with setup disk */
    function fu_disk(string $disk): \HexideDigital\HexideAdmin\Classes\FileUploader
    {
        return file_uploader()->disk($disk);
    }
}

if (!function_exists('fu_url')) {
    /** Get url to file in public storage */
    function fu_url(?string $path): string
    {
        return file_uploader()->url($path);
    }
}
