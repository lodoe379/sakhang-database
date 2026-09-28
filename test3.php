<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$rooms = \App\Models\EmptyRoom::whereNotNull('meter_image')->where('meter_image', '!=', '')->get();
echo "EmptyRoom with images: " . count($rooms) . "\n";
foreach($rooms as $r) {
    echo $r->building . " " . $r->room . " -> " . $r->meter_image . "\n";
}
