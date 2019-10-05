<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Passport;
use Config;
use EloquentFilter\Filterable;

class RequestLog extends Model
{
    use Filterable;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'route_name',
        'request_method',
        'client_id',
        'ip_address',
        'device_id',
        'user_id',
        'response_status',
        'status_code',
        'request_headers',
        'response_headers',
        'request',
        'response'
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'url'=> 'string',
        'route_name'=> 'string',
        'request_method'=> 'string',
        'client_id'=> 'int',
        'ip_address'=> 'string',
        'device_id'=> 'int',
        'user_id'=> 'int',
        'response_status'=> 'string',
        'status_code'=> 'int',
        'request_headers'=> 'array',
        'response_headers'=> 'array',
        'request'=> 'array',
        'response' => 'array'
    ];

    /**
     * To get the request Device informations
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * To get the request Client Informartion
     */
    public function client()
    {
        return $this->belongsTo(Passport::clientModel(), 'client_id');
    }

    /**
     * To get the user who was requested.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
