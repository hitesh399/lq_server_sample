<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DevicePivot extends Pivot
{
    protected $casts = ['settings' => 'array'];
}
