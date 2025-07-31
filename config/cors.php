<?php

return [
    'paths' => ['api/*', 'fib/callback'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // Replace * with your trusted domain in production

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
