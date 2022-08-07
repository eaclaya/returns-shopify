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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'shopify' => [
    	'key' => env('SHOPIFY_KEY'),
    	'host' => env('SHOPIFY_HOST'),
        'shop' => env('SHOPIFY_SHOP'),
        'code' => env('SHOPIFY_CODE'),
        'password' => env('SHOPIFY_PASSWORD'),
        'version' => env('SHOPIFY_VERSION')
    ]
];
