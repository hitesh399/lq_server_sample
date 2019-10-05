<?php
namespace App\ModelFilters;


Trait SortByHelper
{
    public function sortBy($sortBy)
    {
        foreach ($sortBy as $key => $value) {
            $this->orderBy($key, $value);
        }
    }
}
