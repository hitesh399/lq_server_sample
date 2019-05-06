<?php

namespace App\Models;

use Singsys\LQ\Lib\Concerns\LqToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, LqToken, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile_no', 'email_verified_at', 'mobile_no_verified_at', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_no_verified_at' => 'datetime',
        'mobile_no' => 'string',
        'role_id' => 'int',
        'name' => 'string',
        'email' => 'string'
    ];

    /**
     * To get the user role information
     */
    public function role() {
        return $this->belongsTo(Role::class);
    }

    public  function devices() {
        return $this->belongsToMany(Device::class)->withPivot([
            'settings', 'login_index', 'active'
        ])->using(Relations\DevicePivot::class);
    }
}
