<?php

namespace App\Facades;

use Cloudinary\Cloudinary as CloudinarySdk;
use Illuminate\Support\Facades\Facade;

class Cloudinary extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cloudinary';
    }

    public static function upload(string $filePath, array $options = []): mixed
    {
        $cloudinary = new CloudinarySdk(config('cloudinary'));

        return $cloudinary->uploadApi()->upload($filePath, $options);
    }
}
