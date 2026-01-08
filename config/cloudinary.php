<?php
// config/cloudinary.php

return [
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => env('CLOUDINARY_API_KEY', env('CLOUDINARY_KEY')),
        'api_secret' => env('CLOUDINARY_API_SECRET', env('CLOUDINARY_SECRET')),
    ],
    'url' => [
        'secure' => true,
    ],
    'upload' => [
        'folder' => env('CLOUDINARY_UPLOAD_FOLDER', null),
    ],
];
