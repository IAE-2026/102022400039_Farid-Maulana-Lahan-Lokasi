<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/v1/locations', 'GET');
$request->headers->set('X-IAE-KEY', '102022400039');

$response = $kernel->handle($request);

echo 'STATUS: ' . $response->getStatusCode() . PHP_EOL;
echo $response->getContent() . PHP_EOL;
