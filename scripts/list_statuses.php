<?php
// Bootstrap Laravel and print distinct visit statuses

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

// Ensure the application is booted
$kernel->bootstrap();

$statuses = App\Models\Visit::query()->select('status')->distinct()->pluck('status')->toArray();
echo json_encode($statuses, JSON_UNESCAPED_UNICODE) . PHP_EOL;
