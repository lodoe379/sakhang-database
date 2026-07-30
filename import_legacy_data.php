<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MeterReading;

$json_file = __DIR__ . '/legacy/consumers.json';
if (!file_exists($json_file)) {
    echo "legacy/consumers.json not found!\n";
    exit(1);
}

$data = json_decode(file_get_contents($json_file), true);
if (!$data) {
    echo "Invalid JSON data\n";
    exit(1);
}

$count = 0;
foreach ($data as $consumer) {
    $exists = MeterReading::where('building', $consumer['building'])
        ->where('room', $consumer['room'])
        ->where('consumer_id', $consumer['cid'] ?? '')
        ->where('meter_number', $consumer['meter'] ?? '')
        ->exists();
        
    if (!$exists) {
        MeterReading::create([
            'building' => $consumer['building'] ?? '',
            'room' => $consumer['room'] ?? '',
            'name_on_bill' => $consumer['name'] ?? '',
            'in_id' => $consumer['inid'] ?? '',
            'meter_number' => $consumer['meter'] ?? '',
            'consumer_id' => $consumer['cid'] ?? '',
            'account_no' => $consumer['account'] ?? '',
        ]);
        $count++;
    }
}

echo "Successfully imported $count meter readings from legacy/consumers.json.\n";
