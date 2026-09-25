<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'electrical_complaints';

    protected $fillable = [
        'name',
        'building',
        'room',
        'phone',
        'complaint',
        'image',
        'video',
        'done',
        'remark',
        'action_date',
        'user_reply',
        'signature'
    ];

    protected $casts = [
        'done' => 'boolean'
    ];

    public function getSnoAttribute()
    {
        return $this->id;
    }

    public function getDateAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    public function getStatusAttribute()
    {
        return $this->done ? 'Done' : 'Incomplete';
    }
}
