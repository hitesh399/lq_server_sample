<?php
namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ApplicationMenuItemFilter extends ModelFilter
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

    public function applicationMenu($menu_id)
    {
        $this->where('application_menu_items.application_menu_id',  $menu_id);
    }
}
