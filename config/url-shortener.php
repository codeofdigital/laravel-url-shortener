<?php

return [
    'default' => env('URL_SHORTENER_DRIVER', 'tiny_url'),

    'shorteners' => [
        'bit.ly' => [
            'driver' => 'bit_ly',
            'domain' => env('URL_SHORTENER_PREFIX', 'bit.ly'),
            'token' => env('URL_SHORTENER_API_TOKEN'),
        ]
    ]
];
