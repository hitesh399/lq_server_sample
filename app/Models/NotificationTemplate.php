<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class NotificationTemplate extends Model
{
    use Filterable;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'name','subject','body','type','options'
    ];
    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
		'name'		=> 'string',
		'subject'	=> 'string',
		'body'		=> 'string',
		'type'		=> 'string',
		'options'	=> 'json'
    ];
}
