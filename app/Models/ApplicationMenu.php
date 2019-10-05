<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Laravel\Passport\Passport;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class ApplicationMenu extends Model
{
    use Filterable, HasJsonRelationships;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'client_ids', 'role_ids'
    ];
      /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'client_ids' => 'array',
        'role_ids' => 'array',
        'name'=> 'string',
    ];
    public function clients() {
        return $this->belongsToJson(Passport::$clientModel, 'client_ids', 'id');
    }
    public function roles() {
        return $this->belongsToJson(Role::class, 'role_ids', 'id');
    }
    public function items() {
        return $this->belongsToMany(ApplicationMenuItem::class);
    }
}
