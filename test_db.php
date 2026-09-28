<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

print_r(\App\Models\RoomFurniture::all()->toArray());
print_r(\App\Models\MeterReading::all()->toArray());
