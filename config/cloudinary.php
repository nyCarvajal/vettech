<?php
// config/cloudinary.php

$cloudinaryUrl = env('CLOUDINARY_URL');
$cloudinaryUrlParts = $cloudinaryUrl ? parse_url($cloudinaryUrl) : [];

return [
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME', $cloudinaryUrlParts['host'] ?? null),
        'api_key' => env('CLOUDINARY_API_KEY', env('CLOUDINARY_KEY', $cloudinaryUrlParts['user'] ?? null)),
        'api_secret' => env('CLOUDINARY_API_SECRET', env('CLOUDINARY_SECRET', $cloudinaryUrlParts['pass'] ?? null)),
    ],
    'url' => [
        'secure' => true,
    ],
    'upload' => [
        'folder' => env('CLOUDINARY_UPLOAD_FOLDER', null),
    ],
];
