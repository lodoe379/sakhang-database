<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomFurniture extends Model
{
    use HasFactory;

    protected $table = 'room_furnitures';

    protected $fillable = [
        'building',
        'room',
        'bed',
        'table',
        'chair',
        'cupboard'
    ];
}
