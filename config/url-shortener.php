<?php

return [
    'default' => env('URL_SHORTENER_DRIVER', 'bit.ly'),

    'shorteners' => [
        'bit.ly' => [
            'driver' => 'bit_ly',
            'domain' => env('URL_SHORTENER_PREFIX', 'bit.ly'),
            'token' => env('URL_SHORTENER_API_TOKEN'),
        ]
    ]
];
