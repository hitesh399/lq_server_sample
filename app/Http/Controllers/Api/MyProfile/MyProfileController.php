<?php

namespace App\Http\Controllers\Api\MyProfile;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Laravel\Passport\Passport;
use League\OAuth2\Server\CryptTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\Auth\Concerns\FindUser;

/**
 * Edit, View of User Profile.
 *
 * @category My Profile
 *
 * @author   Sachiln Kumar <sachin@singsys.com>
 * @license  PHP License 7.1.25
 *
 * @see
 */
class MyProfileController extends Controller
{
    use FindUser;
    use CryptTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = \Auth::user();
        $role = $user->role()->first();
        $relations = $role->name == 'consultant' ?
            [
                'services' => function ($q) {
                    $q->with(
                        [
                            'image',
                            'serviceUser',
                        ]
                    );
                },
                'consultantRate',
                'idProof',
                'country',
                'city',
                'profileImage',
                'role',
            ]
            :
            [
                'userCards', 'profileImage', 'country', 'city', 'role',
            ];

        $user = User::with($relations)->where('id', $user->id)->first();
        $permissions = $user->role->menuItems()->get()->pluck('name');
        $user->setRelation('permissions', $permissions);

        return $this->setData(
            [
                'user' => $user,
            ]
        )->response();
    }

    /**
     * Update the User Details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'name' => 'max:25',
            'country' => 'int',
            'city' => 'int',
            'email' => 'email|unique:users,email,'.Auth::id(),
            'pincode' => 'between:4, 8',
            'date_of_birth' => 'date|date_format:Y-m-d',
            'mobile_no' => ['regex:/^\+?([0-9]){1,4}-([0-9]){6,12}$/', 'unique:users,mobile_no,'.Auth::id()],
            'address' => 'max:100',
            'about_me' => 'max:255',
        ],
        [
            'mobile_no.regex' => 'The Mobile Number is not valid.',
        ],
        [
            'mobile_no' => 'Mobile Number',
        ]);
        $user = Auth::user();
        $old_email = $user->email;
        $new_email = $request->email;
        $old_number = $user->mobile_no;
        $new_number = $request->mobile_no;
        $email_verified_at = $old_email != $new_email ? null : date('Y-m-d H:i:s');
        $mobile_no_verified_at = $old_number != $new_number ? null : date('Y-m-d H:i:s');
        $user->update(
            [
                'name' => $request->name,
                'email' => $new_email,
                'date_of_birth' => $request->date_of_birth,
                'mobile_no' => $new_number,
                'address' => $request->address,
                'country_id' => $request->country,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'about_me' => $request->about_me,
                'email_verified_at' => $email_verified_at,
                'mobile_no_verified_at' => $mobile_no_verified_at,
            ]
        );

        return $this->setData([
            'data' => $user,
        ])->setMessage('Record Updated Successfully')->response();
    }

    public function myProfilePhoto(Request $request)
    {
        $this->validate($request,
            [
                'profileImage.file' => 'mimes:jpeg,jpg,png,gif',
            ]
        );
        $user = Auth::user();
        $user->profileImage()->addMedia($request->profileImage, 'profile_image');

        return $this->setData(
            [
                'data' => $user,
            ]
        )->response();
    }

    public function appointmentsCounts()
    {
        $user = \Auth::user();
        $role = $user->role()->first();
        $date = date('Y-m-d');
        $appointments = array();

        if ($role->name == 'customer') {
            $condition = 'customer_id';
        } elseif ($role->name === 'consultant') {
            $condition = 'consultant_id';
        } else {
            abort(404);
        }
        $appointments['total_appointment'] = Appointment::where($condition, $user->id)->count();
        $appointments['today_appointment'] = Appointment::where($condition, $user->id)->where('scheduled_at', $date)->count();

        $appointments['past_appointment'] = Appointment::where($condition, $user->id)->whereDate('scheduled_at', '<', $date)->count();

        $appointments['upcoming_appointment'] = Appointment::where($condition, $user->id)
        ->where('scheduled_at', '>', $date)
        ->where('status', 'confirm')
        ->count();

        $appointments['pending_appointment'] = Appointment::where(
            $condition, $user->id
        )->where('status', 'pending')->count();

        return $this->setData($appointments)
            ->response();
    }

    /**
     * To Logout the User and invoke the token if client is ios or android.
     *
     * @param Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $user_id = \Auth::id();
            $socket_id = $request->socket_id;
            if ($socket_id) {
                $joining_key = "active.users.{$user_id}.{$socket_id}";
                Redis::del($joining_key);
            }
            Passport::token()->where(
                'id', $request->user()->token()->id
            )->update(['revoked' => true]);

            $request->user()->devices()->where(
                'devices.id', $request->device()->id
            )->first();
        }

        /*
         * Deactivate other User on Same device.
         */
        $request->device()->users()->syncWithoutDetaching(
            [
                $request->user()->id => ['active' => 'No'],
            ]
        );

        $this->setMessage(trans('auth.logout_success'));

        return $this->response();
    }

    /**
     * To Reset the User password.
     *
     * @param Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => ['required', 'min:8', 'max:16'],
            'confirm_password' => 'required_with:password|same:password',
        ]);

        // When old password does not match.
        if (!\Hash::check($request->old_password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'old_password' => [trans('auth.old_password_wrong')],
            ]);
        }
        $request->user()->update(['password' => \Hash::make($request->password)]);

        $this->setMessage(trans('auth.password_changed_success'));

        return $this->response();
    }

    /**
     * My Profile Status.
     */
    public function myProfileStatus(Request $request)
    {
        $user = \Auth::user();
        $not_completed = [];
        if ($user->role->name == 'consultant') {
            if (!$user->stripe_account_id) {
                $not_completed[] = 'stripe_account';
            }
            $service = $user->services()->whereNotNull('base_charge')->first();
            if (!$service) {
                $not_completed[] = 'service_price';
            }
        } else {
            $card = $user->userCards()->first();
            if (!$card) {
                $not_completed[] = 'card';
            }
        }

        return $this->setData(['not_completed' => $not_completed])->response();
    }
}
