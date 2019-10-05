<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class RolePermission extends Model
{
    use Filterable;

    public function rolePermissionFields() {

        return $this->hasMany(RolePermissionField::class, 'role_permission_id');
    }
    /**
     * Get the Permission information
     */
    public function permission() {

        return $this->belongsTo(Permission::class);
    }
}
