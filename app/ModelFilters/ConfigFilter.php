<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ConfigFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function setup() {

        $this->select(['id','name','data','config_group','options']);
    }
    /**
     * keyword search base on name and config_group
     */
    public function search($val) {
        $this->where(function ($q) use ($val) {
            $q->orWhere('name', 'LIKE', "%{$val}%")
                ->orWhere('config_group', 'LIKE', "%{$val}%");
            }
        );
    }
    public function sortBy($val) {
        foreach($val as $key => $value) {
            $this->orderBy($key, $value);
        }
    }
}
