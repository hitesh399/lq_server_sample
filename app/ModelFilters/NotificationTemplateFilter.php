<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class NotificationTemplateFilter extends ModelFilter
{
    use SortByHelper;
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function setup() {
        $this->select(['id','name','subject','body','type','options']);

    }
    public function search($val) {
        $this->where(function ($q) use ($val) {
            $q->where('name','LIKE', "%{$val}%")
                ->orWhere('subject','LIKE',"%{$val}%")
                ->orWhere('body','LIKE',"%{$val}%")
                ->orWhere('type','LIKE',"%{$val}%")
                ->orWhere('options','LIKE',"%{$val}%");
        });
    }

    public function type($val) {
        $this->where('type', $val);
    }

}
