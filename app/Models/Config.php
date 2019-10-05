<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use App\Lib\MyApp;

class Config extends Model
{
    use Filterable;
    protected $table = 'site_config';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'data', 'config_group', 'options',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'data' => 'string',
        'config_group' => 'string',
        'options' => 'json',
    ];

    protected function getDataAttribute($data)
    {
        return $data && MyApp::isJson($data) ? json_decode($data) : $data;
    }
}
