<?php
namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ApplicationMenuFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    function setup() {

    }

    public function role($role_id)
    {
        $this->whereJsonContains('role_ids', $role_id);
    }
}
