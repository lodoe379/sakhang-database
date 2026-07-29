<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $fillable = [
        'building',
        'room',
        'name_on_bill',
        'in_id',
        'meter_number',
        'consumer_id',
        'account_no',
        'meter_image',
    ];
}
