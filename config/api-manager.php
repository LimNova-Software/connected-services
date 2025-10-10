<?php

declare(strict_types=1);

return [
    'default' => env('API_MANAGER_DEFAULT_CONNECTOR', 'google'),

    'connectors' => [
        'google' => [
            'base_url' => env('GOOGLE_API_BASE_URL', 'https://www.googleapis.com'),
            'credentials' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
                'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            ],
            'options' => [
                'timeout' => env('GOOGLE_API_TIMEOUT', 30),
                'retries' => env('GOOGLE_API_RETRIES', 3),
            ],
            'default_headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ],

        'exactonline' => [
            'base_url' => env('EXACT_ONLINE_API_BASE_URL', 'https://start.exactonline.nl'),
            'credentials' => [
                'client_id' => env('EXACT_ONLINE_CLIENT_ID'),
                'client_secret' => env('EXACT_ONLINE_CLIENT_SECRET'),
                'division' => env('EXACT_ONLINE_DIVISION'),
                'redirect_uri' => env('EXACT_ONLINE_REDIRECT_URI'),
            ],
            'options' => [
                'timeout' => env('EXACT_ONLINE_API_TIMEOUT', 30),
                'retries' => env('EXACT_ONLINE_API_RETRIES', 3),
            ],
            'default_headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ],
    ],

    'cache' => [
        'enabled' => env('API_MANAGER_CACHE_ENABLED', true),
        'ttl' => env('API_MANAGER_CACHE_TTL', 3600),
    ],

    'logging' => [
        'enabled' => env('API_MANAGER_LOGGING_ENABLED', true),
        'level' => env('API_MANAGER_LOG_LEVEL', 'info'),
    ],
];
