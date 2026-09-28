<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
print_r(\Illuminate\Support\Facades\Schema::getColumnListing('empty_rooms'));
print_r(\Illuminate\Support\Facades\Schema::getColumnListing('meter_readings'));
