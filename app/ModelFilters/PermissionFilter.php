<?php namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class PermissionFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function setup() {

        $this->select(['id', 'name', 'is_public', 'title', 'description', 'encrypted', 'permission_group_id', 'limitations']);
    }
    public function search($val) {

        $this->where(function($q) use ($val) {

            $q->orWhere('permissions.name', 'LIKE', "%{$val}%");
            $q->orWhere('permissions.title', 'LIKE', "%{$val}%");
            $q->orWhere('permissions.description', 'LIKE', "%{$val}%");
        });
    }

    public function permissionGroup($val) {

        $this->where('permissions.permission_group_id', $val);
    }

    public function sortBy($val) {
        foreach($val as $key => $value){
            $sort = $value == 'ascending' ? 'ASC' : 'DESC';
            $this->orderBy($key,$sort);
        }
    }
}
