<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$emptyRooms = \App\Models\EmptyRoom::all();
$count = 0;
foreach ($emptyRooms as $er) {
    // Migrate to RoomFurniture if not exists
    $rf = \App\Models\RoomFurniture::where('building', $er->building)->where('room', $er->room)->first();
    if (!$rf) {
        $rf = new \App\Models\RoomFurniture();
        $rf->building = $er->building;
        $rf->room = $er->room;
        $rf->bed = $er->bed ?? '';
        $rf->table = $er->table ?? '';
        $rf->chair = $er->chair ?? '';
        $rf->cupboard = $er->cupboard ?? '';
        $rf->save();
    }
    
    // Migrate to MeterReading if not exists
    $mr = \App\Models\MeterReading::where('building', $er->building)->where('room', $er->room)->first();
    if (!$mr) {
        $mr = new \App\Models\MeterReading();
        $mr->building = $er->building;
        $mr->room = $er->room;
        $mr->name_on_bill = $er->name_on_bill ?? '';
        $mr->in_id = $er->in_id ?? '';
        $mr->meter_number = $er->meter_number ?? '';
        $mr->consumer_id = $er->consumer_id ?? '';
        $mr->account_no = $er->account_no ?? '';
        $mr->meter_image = $er->meter_image ?? '';
        $mr->save();
    }
    $count++;
}
echo "Migrated $count records from EmptyRoom to MeterReading and RoomFurniture!\n";
