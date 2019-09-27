<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name', 'file_size', 'path', 'token', 'device_id', 'client_id',
    ];
}
