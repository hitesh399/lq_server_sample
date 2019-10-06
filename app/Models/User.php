<?php

namespace App\Models;

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'timezone', 'password', 'mobile_no', 'email_verified_at', 'mobile_no_verified_at', 'role_id', 'status',
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
        'role_id' => 'int',
        'name' => 'string',
        'email' => 'string',
    ];

    /**
     * To get the user role information.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function devices()
    {
        return $this->belongsToMany(Device::class)->withPivot([
            'settings', 'login_index', 'active',
        ])->using(Relations\DevicePivot::class);
    }

    public function profileImage()
    {
        return $this->morphOneMedia(\Config::get('lq.media_model_instance'), 'mediable', 'image', __FUNCTION__);
    }

    public function photos()
    {
        return $this->morphManyMedia(\Config::get('lq.media_model_instance'), 'mediable', 'user_photos', __FUNCTION__);
    }
}
