<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Provider
    |--------------------------------------------------------------------------
    */

    'provider' => env('EMISSIONS_PROVIDER', 'lune'),

    /*
    |--------------------------------------------------------------------------
    | Provider Definitions
    |--------------------------------------------------------------------------
    |
    | Each provider defines:
    | - class: implementation FQCN
    | - config: constructor parameters
    |
    */

    'providers' => [

        'lune' => [

            'class' => \Ceedbox\LuneModule\LuneClient::class,

            'config' => [
                'orgId'   => env('LUNE_ORG_ID'),
                'apiKey'  => env('LUNE_API_KEY'),
                'baseUrl' => env('LUNE_BASE_URL', 'https://sustainability.lune.co'),
                'ttl'     => env('LUNE_TOKEN_TTL', 3600),
            ],

        ],

    ],

];