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
    'currency_conversion' => [
        'base_uri' => env('EXCHANGE_CURRENCY_BASE_URI'),
        'api_key' => env('EXCHANGE_CURRENCY_API_KEY'),
    ],
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'paypal' => [
        'base_uri' => env('PAYPAL_BASE_URI'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'mode' => env('PAYPAL_MODE'),
        'class' => App\Services\PayPalService::class
    ],

      'stripe' => [
        'base_uri' => env('STRIPE_BASE_URI'),
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'account' => env('STRIPE_ACCOUNT'),
        'class' => App\Services\StripeService::class
    ],
      'mercado_pago' => [
        'base_uri' => env('MERCADO_PAGO_BASE_URI'),
        'public_key' => env('MERCADO_PAGO_PUBLIC_KEY'),
        'access_token' => env('MERCADO_PAGO_ACCESS_TOKEN'),
        'client_id' => env('MERCADO_PAGO_CLIENT_ID'),
        'client_secret' => env('MERCADO_PAGO_CLIENT_SECRET'),
        'base_currency' => 'ars',
        'class' => App\Services\MercadoPagoService::class
    ],
    
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
