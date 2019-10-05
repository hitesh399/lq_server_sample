<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePivot extends Pivot {

    protected $casts = ['limitations' => 'array'];
}
