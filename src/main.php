<?php

declare(strict_types=1);

use App\BinListProvider;
use App\RatesProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App\CommissionService(new BinListProvider(), new RatesProvider());

try {
    print_r($app->handle($argv[1]));
} catch (\Exception $exception) {
    echo $exception->getMessage();
}

