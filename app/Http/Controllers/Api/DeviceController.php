<?php

namespace App\Http\Controllers\Api;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ModelFilters\Universal\DeviceFilter;
use Illuminate\Validation\ValidationException;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request [All Request data]
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $devices = Device::filter(
            $request->all(), DeviceFilter::class
        )->paginate($request->page_size);

        return $this->setData($devices)
            ->response();
    }

    /**
     * To Remove the user from device.
     *
     * @param Illuminate\Http\Request $request [All Request]
     * @param int                     $user_id [User Table primary key]
     *
     * @return \Illuminate\Http\Response
     */
    public function revokedDeviceUser(Request $request, $user_id)
    {
        $user_device = $request->device()->users()->where('users.id', $user_id)->first();
        if ($user_device) {
            $request->device()->users()->syncWithoutDetaching([$user_id => ['active' => 'No', 'revoked' => '1']]);
        }

        return $this->setMessage('User Has been revoked.')->response();
    }

    /**
     * To get login user of the given device.
     *
     * @param Illuminate\Http\Request $request [All Request]
     *
     * @return \Illuminate\Http\Response
     */
    public function deviceLoginUser(Request $request)
    {
        $users = $request->device()->users()
            ->where('device_user.revoked', '0')
            ->select(['users.id', 'users.name'])->get();

        return $this->setData(
            [
                'device_users' => $users,
            ]
        )->response();
    }

    /**
     * To Switch the role.
     *
     * @param \Illuminate\Http\Request $request [All Request data]
     * @param int                      $role_id [Role table primary key]
     *
     * @return \Illuminate\Http\Response
     */
    public function switchRole(Request $request, $role_id)
    {
        $has_role = $request->user()->roles()->where('roles.id', $role_id)->first();
        if (!$has_role) {
            $this->setErrorCode('role_not_allowed');
            throw ValidationException::withMessages([]);
        }

        $request->device()->users()
            ->syncWithoutDetaching(
                [
                    \Auth::id() => [
                        'active' => 'Yes',
                        'role_id' => $role_id,
                        'revoked' => '0',
                    ],
                ]
            );
        $permission = app('permission');
        $permission->setCurrentRoleIds([$role_id]);
        $my_profile = new Auth\MyProfileController();

        return $my_profile->index($request);
    }
}
