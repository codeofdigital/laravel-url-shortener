<?php

return [
    'default' => env('URL_SHORTENER_DRIVER', 'bit_ly'),

    'shorteners' => [

        'bit_ly' => [
            'driver' => 'bit_ly',
            'domain' => env('URL_SHORTENER_PREFIX', 'bit.ly'),
            'token' => env('URL_SHORTENER_API_TOKEN')
        ],

        'tiny_url' => [
            'driver' => 'tiny_url',
            'domain' => env('URL_SHORTENER_PREFIX', 'tinyurl.com'),
            'token' => env('URL_SHORTENER_API_TOKEN')
        ],

        'shorte_st' => [
            'driver' => 'shorte_st',
            'token' => env('URL_SHORTENER_API_TOKEN')
        ],

        'is_gd' => [
            'driver' => 'is_gd',
            'statistic' => env('URL_SHORTENER_ANALYTICS', false)
        ],

        'cutt_ly' => [
            'driver' => 'cutt_ly',
            'token' => env('URL_SHORTENER_API_TOKEN')
        ]
    ]
];
