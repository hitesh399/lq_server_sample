<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Laravel\Passport\Passport;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Role extends Model
{
    use Filterable;
    use HasJsonRelationships;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'parent_role_id', 'title', 'description', 'client_ids', 'choosable', 'settings',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'client_ids' => 'array',
        'settings' => 'array',
        'parent_role_id' => 'int',
        'title' => 'string',
        'name' => 'string',
        'description' => 'string',
        'choosable' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * To get the all permission of a role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')->withPivot([
            'limitations', 'id',
        ])->using(Relations\RolePivot::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(ApplicationMenuItem::class, 'role_menu_item', 'role_id', 'menu_item_id');
    }

    public function clients()
    {
        return $this->belongsToJson(Passport::$clientModel, 'client_ids', 'id');
    }

    public function rolePermissionFields()
    {
        return $this->hasMany(RolePermissionField::class);
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class)->with('permission');
    }

    /**
     * To get the all permission fields of as role.
     */
    public function permissionFields()
    {
        return $this->belongsToMany(RolePermission::class, 'role_permission_fields', 'role_id', 'role_permission_id')->withPivot([
            'permission_field_id', 'id', 'permission_id', 'authority',
        ]);
    }
}
