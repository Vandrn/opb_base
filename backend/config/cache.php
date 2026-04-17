<?php

return [
    'default' => env('CACHE_DRIVER', 'array'),

    'stores' => [
        'array' => [
            'driver' => 'array',
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],

    'prefix' => env('CACHE_PREFIX', 'opb_cache'),
];
