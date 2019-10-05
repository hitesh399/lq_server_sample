<?php

namespace App\Http\Controllers\Api\Developer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeveloperController extends Controller
{
    //
    /**
     * To Execute the PHP command
     */
    public function executeLaravelCommand(Request $request)
    {
        $data = shell_exec('cd '. base_path().' && php artisan '.$request->command);
        return $this->setData([
            'output' => $data
        ])->response();
    }
}
