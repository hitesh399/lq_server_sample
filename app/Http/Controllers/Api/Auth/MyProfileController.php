<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use App\Http\Controllers\Controller;

class MyProfileController extends Controller
{
    use Concerns\FindUser;

    public function index(Request $request) {
        $this->findUser($request, auth()->user()->id);
        $device = $this->user->devices()->where('devices.id', $request->device()->id)->first();
        # send user information
        return $this->setData([
            'user' => $this->user,
            'device' => $device['pivot']
        ])
        ->response();
    }
    /**
     * To Logout the User and invoke the token if client is ios or android
     * @param Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Passport::token()->where('id', $request->user()->token()->id)->update(['revoked' => true]);
        $user_device = $request->user()->devices()->where('devices.id', $request->device()->id)->first();

        if($user_device) {
            $request->device()->users()->syncWithoutDetaching([
                $request->user()->id => ['active' => 'No']
            ]);
        }

        $this->setMessage(trans('auth.logout_success'));
        return $this->response();
    }
}
