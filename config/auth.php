<?php

return [

    'defaults' => [
        // Default remains the same for regular users
        'guard' => 'sanctum',
        'passwords' => 'users',
    ],

    'guards' => [

        // Web-based admin guard (unchanged)
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        // Regular user API guard (unchanged)
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],

        // ✅ New API guard for Account users (hotel, motel, etc.)
        'account' => [
            'driver' => 'sanctum',
            'provider' => 'accounts',
        ],
    ],

    'providers' => [

        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        // ✅ New provider for Account model
        'accounts' => [
            'driver' => 'eloquent',
            'model' => App\Models\Account::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        // You can add a password reset config for 'accounts' later if needed
    ],

    'password_timeout' => 10800,

];
