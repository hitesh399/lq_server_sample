<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\ModelFilters\UserFilter;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Display a listing of the Consultant based on services.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admins = User::filter(
            $request->all(),
            UserFilter::class
        )->select(['users.*'])->groupBy('users.id')
            ->lqPaginate();

        return $this->setData($admins)
            ->response();
    }

    /**
     * Store a newly Consultant User.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:100',
                'email' => 'required|email|unique:users|max:150',
                'roles' => 'required|array',
                'mobile_no' => [
                    'required',
                    'unique:users',
                    'regex:/^\+?([0-9]){1,5}-([0-9]){6,12}$/',
                ],
            ],
            [
                'mobile_no.regex' => 'The Mobile Number is not valid.',
            ],
            [
                'mobile_no' => 'Mobile Number',
            ]
        );

        $user_data = $request->only(
            ['name', 'email', 'mobile_no']
        );
        $password = Str::random(8);
        $user_data = array_merge(
            $user_data,
            [
                'password' => \Hash::make($password),
                'status' => 'active',
            ]
        );
        $user = User::create($user_data);
        $user->roles()->sync($request->roles);

        event(new Registered($user, $password));

        return $this->setData(['user' => $user])
            ->setMessage('Admin account has been created successfully.')
            ->response();
    }

    /**
     * Update the specific Consultant User.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'name' => 'max:25',
                'email' => 'email|unique:users,email,'.$id,
                'roles' => 'required|array',
                'mobile_no' => [
                    'regex:/^\+?([0-9]){1,4}-([0-9]){6,12}$/',
                    'unique:users,mobile_no,'.$id,
                ],
            ],
            [
                'mobile_no.regex' => 'The Mobile Number is not valid.',
            ],
            [
                'mobile_no' => 'Mobile Number',
            ]
        );
        $user = User::find($id);
        $user->update(
            [
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
            ]
        );

        $user->roles()->sync($request->roles);

        return $this->setData(['data' => $user])
            ->setMessage('Admin Updated Successfully')->response();
    }

    /**
     * view details of consultant user.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id)->load('roles');

        return $this->setData(['data' => $user])->response();
    }

    /**
     * Activate/Deactivate account of consultant by the Admin.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function userChangeStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->status === 'active') {
            $user->status = 'inactive';
            $user->save();

            return $this->setMessage('Deactivated Successfully')->response();
        } elseif ($user->status === 'inactive') {
            $user->status = 'active';
            $user->save();

            return $this->setMessage('Activated Successfully')->response();
        }

        return $this->setMessage('User not found')
            ->setErrorCode('user_not_found')
            ->response();
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->forceDelete();

        return $this->setMessage('Admin has been deleted.')->response();
    }
}
