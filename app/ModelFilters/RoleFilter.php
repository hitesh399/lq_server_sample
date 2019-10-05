<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Http\Request;

class RoleFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function setup()
    {
        $this->select('id', 'name', 'parent_role_id', 'title', 'description', 'client_ids', 'choosable');
    }

    public function search($val)
    {
        $this->where(function ($q) use ($val) {
            $q->orWhere('roles.name', 'LIKE', "%{$val}%");
            $q->orWhere('roles.title', 'LIKE', "%{$val}%");
            $q->orWhere('roles.description', 'LIKE', "%{$val}%");
        });
    }

    public function myAppRole()
    {
        $client_id = app()->make(Request::class)->client()->id;
        $this->whereJsonContains('client_ids', $client_id);
    }

    public function sortBy($val)
    {
        foreach ($val as $key => $value) {
            $this->orderBy($key, $value);
        }
    }

    public function notName($val)
    {
        $this->whereNotIn('roles.name', $val);
    }
}
