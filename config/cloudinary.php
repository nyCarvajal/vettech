<?php
// config/cloudinary.php

$cloudinaryUrl = env('CLOUDINARY_URL');
$cloudinaryUrlParts = $cloudinaryUrl ? parse_url($cloudinaryUrl) : [];
$cloudinaryUrlParts = is_array($cloudinaryUrlParts) ? $cloudinaryUrlParts : [];

$cloudinaryUrlFallback = [];
if ($cloudinaryUrl && preg_match('~^cloudinary://([^:]+):([^@]+)@([^/?]+)~', $cloudinaryUrl, $matches)) {
    $cloudinaryUrlFallback = [
        'user' => rawurldecode($matches[1]),
        'pass' => rawurldecode($matches[2]),
        'host' => $matches[3],
    ];
}

$cloudinaryCloudNameFromPath = null;
if (! empty($cloudinaryUrlParts['path'])) {
    $cloudinaryCloudNameFromPath = ltrim($cloudinaryUrlParts['path'], '/');
}

return [
    'cloud' => [
        'cloud_name' => env(
            'CLOUDINARY_CLOUD_NAME',
            $cloudinaryUrlParts['host']
                ?? $cloudinaryUrlFallback['host']
                ?? $cloudinaryCloudNameFromPath
                ?? null
        ),
        'api_key' => env('CLOUDINARY_API_KEY', env('CLOUDINARY_KEY', $cloudinaryUrlParts['user'] ?? $cloudinaryUrlFallback['user'] ?? null)),
        'api_secret' => env('CLOUDINARY_API_SECRET', env('CLOUDINARY_SECRET', $cloudinaryUrlParts['pass'] ?? $cloudinaryUrlFallback['pass'] ?? null)),
    ],
    'url' => [
        'secure' => true,
    ],
    'upload' => [
        'folder' => env('CLOUDINARY_UPLOAD_FOLDER', null),
    ],
];
