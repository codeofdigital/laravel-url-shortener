<?php

return [
    'default' => env('URL_SHORTENER_DRIVER', 'bit_ly'),

    'shorteners' => [
        'bit_ly' => [
            'driver' => 'bit_ly',
            'domain' => env('URL_SHORTENER_PREFIX', 'bit.ly'),
            'token' => env('URL_SHORTENER_API_TOKEN'),
        ]
    ]
];
