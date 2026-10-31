<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Local demonstration data
    |--------------------------------------------------------------------------
    |
    | Demo records are opt-in and must never be enabled in production. The
    | password intentionally has no source-controlled default value.
    |
    */
    'seed_enabled' => env('DEMO_SEED_ENABLED', false),
    'seed_password' => env('DEMO_SEED_PASSWORD'),
];
