<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FurnitureLog extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'official_name',
        'type',
        'return_date',
        'items',
        'application',
        'remark',
        'action_date',
        'done'
    ];

    protected $casts = [
        'items' => 'array'
    ];
}
