<?php

require __DIR__ . '/../../vendor/autoload.php';

use Ceedbox\LuneModule\LuneClient;

$client = new LuneClient(
    'ORG123',
    'secret',
    'https://sustainability.lune.co'
);

echo $client->dashboardUrl('CLIENT1') . PHP_EOL;