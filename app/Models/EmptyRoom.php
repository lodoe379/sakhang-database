<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmptyRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'building',
        'room',
        'bed',
        'table',
        'chair',
        'cupboard',
        'name_on_bill',
        'in_id',
        'meter_number',
        'consumer_id',
        'account_no',
        'meter_image',
    ];
}
