<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Role;
use App\Models\User;
use App\Rules\MobileNo;
use App\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\UniqueAttributeWithTrashed;

/**
 * To User Regiatration.
 *
 * @category Auth
 *
 * @author  Hitesh Kumar <live2hitesh@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @see https://github.com/hitesh399
 */
class RegisterController extends Controller
{
    /**
     * Service to create new user.
     *
     * @param Illuminate\Http\Request $request [Request]
     *
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255', new UniqueAttributeWithTrashed(null, User::class),
                ],
                'mobile_no' => [
                    new MobileNo(),
                    'max:20',
                    new UniqueAttributeWithTrashed(null, User::class),
                ],
                'password' => ['required', 'max:12', 'confirmed'],
                'password_confirmation' => ['required'],
            ]
        );

        $role = Role::where('name', 'visitor')->first();
        $user_data = array_merge(
            $request->only(['name', 'email', 'mobile_no']),
            [
                'password' => \Hash::make($request->password),
            ]
        );
        $user = User::create($user_data);
        if ($role) {
            $user->roles()->sync([$role->id]);
        }
        event(new Registered($user));

        return $this->setData(
            [
                'user' => $user,
            ]
        )->response();
    }
}
