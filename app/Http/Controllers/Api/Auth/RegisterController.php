<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\UniqueAttributeWithTrashed;
use App\Rules\MobileNo;
use App\Models\User;
use App\Models\Role;
use App\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Service to create new user
     * @param Illuminate\Http\Request
     * @param Illuminate\Http\Response
     */
    public function index(Request $request) {

        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', new UniqueAttributeWithTrashed(null, User::class)],
            'mobile_no' => [new MobileNo, 'max:20', new UniqueAttributeWithTrashed(null, User::class)],
            'password' => ['required', 'max:12', 'confirmed'],
            'password_confirmation' => ['required']
        ]);

        $role = Role::where('name', 'visitor')->first();
        $user_data = array_merge($request->only(['name', 'email', 'mobile_no']), [
            'role_id' => $role->id,
            'password' => \Hash::make($request->password)
        ]);
        $user = User::create($user_data);
        event(new Registered($user));
        return $this->setData([
            'user' => $user
        ])
        ->response();
    }
}
