<?php

namespace App\Models;

use App\Services\SendMail;
use EloquentFilter\Filterable;
use Laravel\Passport\HasApiTokens;
use Singsys\LQ\Lib\Concerns\LqToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Singsys\LQ\Lib\Media\Relations\Concerns\HasMediaRelationships;

class User extends Authenticatable
{
    use Notifiable;
    use LqToken;
    use SoftDeletes;
    use HasApiTokens;
    use HasMediaRelationships;
    use Filterable;
    use SendMail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'timezone', 'password', 'mobile_no', 'email_verified_at',
        'mobile_no_verified_at', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
        'name' => 'string',
        'email' => 'string',
    ];

    /**
     * To Get User Role.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * To get User Devices.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function devices()
    {
        return $this->belongsToMany(
            Device::class
        )->withPivot(
            [
                'settings', 'login_index', 'active', 'role_id',
            ]
        )->using(Relations\DevicePivot::class);
    }

    /**
     * To get User Profile Image.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function profileImage()
    {
        return $this->morphOneMedia(
            \Config::get('lq.media_model_instance'),
            'mediable',
            'image',
            __FUNCTION__
        );
    }

    /**
     * To get application role access type one_at_time or many_at_time.
     *
     * @return string
     */
    public function getRoleAccessTypeAttribute()
    {
        return request()->client()->role_access_type;
    }
}
