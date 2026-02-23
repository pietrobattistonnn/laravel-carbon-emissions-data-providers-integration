<?php

return [

    'org_id'   => env('LUNE_ORG_ID'),
    'api_key'  => env('LUNE_API_KEY'),
    'base_url' => env('LUNE_BASE_URL', 'https://sustainability.lune.co'),
    'ttl'      => env('LUNE_TOKEN_TTL', 3600),

];