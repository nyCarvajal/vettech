<?php

return [
    'default_validity_days' => 5,
    'default_declaration' => 'Certifico que el animal ha sido evaluado clínicamente y se encuentra apto para viajar.',
    'default_clinic' => [
        'name' => env('CLINIC_NAME', ''),
        'nit' => env('CLINIC_NIT', ''),
        'address' => env('CLINIC_ADDRESS', ''),
        'phone' => env('CLINIC_PHONE', ''),
        'city' => env('CLINIC_CITY', ''),
        'vet_name' => env('CLINIC_VET_NAME', ''),
        'vet_license' => env('CLINIC_VET_LICENSE', ''),
    ],
];
