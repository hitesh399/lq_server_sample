<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Passport;

class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'device_id', 'device_token', 'info', 'client_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'device_id' => 'string',
        'client_id' => 'int',
        'device_token' => 'string',
        'info' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Passport::clientModel());
    }

    /**
     * To get the device user list.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot([
            'settings', 'login_index', 'active', 'role_id',
        ])->using(Relations\DevicePivot::class);
    }
}
