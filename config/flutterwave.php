<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Flutterwave API Keys
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for the Flutterwave payment gateway.
    | These values will be loaded from the environment variables.
    |
    */

    'public_key' => env('FLUTTERWAVE_PUBLIC_KEY', ''),
    'secret_key' => env('FLUTTERWAVE_SECRET_KEY', ''),
    'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY', ''),
    'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Flutterwave Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the environment for the Flutterwave API.
    | Available options: 'sandbox', 'live'
    |
    */
    'environment' => env('FLUTTERWAVE_ENVIRONMENT', 'sandbox'),
    
    /*
    |--------------------------------------------------------------------------
    | Flutterwave Logo URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL to your logo that will be displayed on the 
    | Flutterwave checkout page.
    |
    */
    'logo_url' => env('FLUTTERWAVE_LOGO_URL', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Flutterwave Webhook URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that Flutterwave will send webhook notifications to.
    | You should set this in your Flutterwave dashboard.
    |
    */
    'webhook_url' => env('APP_URL') . '/api/flutterwave/webhook',
];
