<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Laravel\Passport\Passport;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Permission extends Model
{
    use Filterable, HasJsonRelationships;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_public', 'title', 'description', 'encrypted', 'permission_group_id', 'limitations', 'client_ids', 'specific_role_ids'
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'=> 'string',
        'is_public'=> 'string',
        'title'=> 'string',
        'description'=> 'string',
        'permission_group_id'=> 'int',
        'limitations'=> 'json',
        'encrypted'=> 'json',
        'specific_role_ids'=> 'json',
        'client_ids'=> 'json'
    ];

    /**
     * To get the permission group detail
     */
    public function permissionGroup() {
        return $this->belongsTo(PermissionGroup::class);
    }

    /**
     * To get the permission field data
     */
    public function permissionFields() {
        return $this->hasMany(PermissionField::class);
    }
    public function clients() {
        return $this->belongsToJson(Passport::$clientModel, 'client_ids', 'id');
    }
    public function specificRoles() {
        return $this->belongsToJson(Role::class, 'specific_role_ids', 'id');
    }
}
