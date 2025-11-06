<?php

use Illuminate\Support\Str;

return [

    // Mặc định dùng 'file' để không cần bảng DB cho cache
    'default' => env('CACHE_STORE', 'file'),

    'stores' => [

        'array' => [
            'driver'    => 'array',
            'serialize' => false,
        ],

        'file' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/data'),
            // lock_path không cần chỉ định riêng (Laravel tự dùng path trên)
        ],

        'database' => [
            'driver'          => 'database',
            'connection'      => env('DB_CACHE_CONNECTION'),
            'table'           => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            // 'lock_table' không phải option chuẩn → bỏ để tránh nhầm
        ],

        'redis' => [
            'driver'          => 'redis',
            'connection'      => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        'memcached' => [
            'driver'        => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl'          => [env('MEMCACHED_USERNAME'), env('MEMCACHED_PASSWORD')],
            'options'       => [],
            'servers'       => [
                [
                    'host'   => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port'   => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'dynamodb' => [
            'driver'   => 'dynamodb',
            'key'      => env('AWS_ACCESS_KEY_ID'),
            'secret'   => env('AWS_SECRET_ACCESS_KEY'),
            'region'   => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table'    => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

        // Nếu muốn failover: thử 'file' trước, lỗi mới dùng 'array'
        'failover' => [
            'driver' => 'failover',
            'stores' => [
                env('CACHE_PRIMARY', 'file'),
                'array',
            ],
        ],
    ],

    'prefix' => env(
        'CACHE_PREFIX',
        Str::slug((string) env('APP_NAME', 'laravel'), '_').'_cache'
    ),

];
