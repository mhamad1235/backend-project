<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    
    'otpiq' => [
    'key' => env('OTPIQ_API_KEY'),
    'url' => env('OTPIQ_API_URL'),
],
    
    'fib' => [
    'client_id' => env('FIB_CLIENT_ID'),
    'client_secret' => env('FIB_CLIENT_SECRET'),
    'base_url' => env('FIB_BASE_URL', 'https://fib.stage.fib.iq'),
    'callback_url' => 'https://c1a858ca31c0.ngrok-free.app/api/callback'
    
    ],



];
