<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'parent_role_id', 'title', 'description', 'client_ids', 'choosable', 'landing_portal', 'settings'
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'client_ids' => 'array',
        'settings' => 'array',
        'parent_role_id'=> 'int',
        'title'=> 'string',
        'name'=> 'string',
        'description'=> 'string',
        'choosable'=> 'string',
        'landing_portal'=> 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
