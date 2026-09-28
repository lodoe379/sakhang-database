<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$room = \App\Models\EmptyRoom::where('building', 'Ganden Khang (Chitue)')->where('room', '1')->first();
if ($room) {
    print_r($room->toArray());
} else {
    echo "Not found";
}
