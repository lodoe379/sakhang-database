<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$request = new \Illuminate\Http\Request();
$request->merge(['building' => 'Ganden Khang (Chitue)', 'room' => '2']);
$controller = new \App\Http\Controllers\DashboardController();
$response = $controller->searchRoomData($request);
echo $response->content();
