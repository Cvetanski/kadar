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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-haiku-4-5-20251001'),
    ],

    'paddle' => [
        'api_key' => env('PADDLE_API_KEY'),
        'client_side_token' => env('PADDLE_CLIENT_SIDE_TOKEN'),
        'webhook_secret' => env('PADDLE_WEBHOOK_SECRET'),
        'sandbox' => env('PADDLE_SANDBOX', false),

        // Live and sandbox are separate Paddle environments with their own
        // catalogs, so each needs its own set of price IDs — not secret,
        // just identifiers. Which map is used is controlled by 'sandbox'
        // above (PADDLE_SANDBOX), so the same code works in both.
        'prices' => [
            'creator_monthly' => 'pri_01m34b90f10mw2jd1r23vbnrcx',
            'creator_annual' => 'pri_01m34b9qqevvexw688agfm8cxf',
            'client_monthly' => 'pri_01m34b5dy1207mxa96ra27dj39',
            'client_annual' => 'pri_01m34b74taf5x4gq0q8gv1r2bk',
        ],

        // Fill these in once the matching sandbox products/prices exist.
        'sandbox_prices' => [
            'creator_monthly' => env('PADDLE_SANDBOX_PRICE_CREATOR_MONTHLY'),
            'creator_annual' => env('PADDLE_SANDBOX_PRICE_CREATOR_ANNUAL'),
            'client_monthly' => env('PADDLE_SANDBOX_PRICE_CLIENT_MONTHLY'),
            'client_annual' => env('PADDLE_SANDBOX_PRICE_CLIENT_ANNUAL'),
        ],
    ],

];
