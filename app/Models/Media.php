<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
     protected $fillable = [
        'path',
        'type',
        'user_id',
        'thumbnails',
        'info',
        'mediable_id',
        'mediable_type',
        'created_at'
    ];
}
