<?php

declare(strict_types=1);

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'tiktok' => [
        'uri' => env('TIKTOK_URI', 'https://open-api.tiktok.com'),
        'client_id' => env('TIKTOK_CLIENT_ID'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
        'redirect' => env('TIKTOK_REDIRECT_URI'),
        'users' => [
            'secret_pass' => env('TIKOK_GENERIC_PASS', 'fakePass')
        ]
    ],
    'ipinfo' => [
        'access_token' => env('IPINFO_SECRET', '')
    ],
    'faker' => [
        'pass' => env('MAGICLINK_PASS')
    ],
    'otp' => [
        'digits' => 6,
        'validity' => 15 //mins
    ],
    'redirects' => [
        'tiktok' => env('VUE2_APP_URL') . 'tiktok/?',
        'errors' =>  env('VUE2_APP_URL') . 'errors/?',
    ]
];
