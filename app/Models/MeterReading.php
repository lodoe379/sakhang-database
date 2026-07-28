<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $fillable = [
        'building',
        'room',
        'consumer_id',
        'in_id',
        'meter_number',
        'meter_image',
    ];
}
