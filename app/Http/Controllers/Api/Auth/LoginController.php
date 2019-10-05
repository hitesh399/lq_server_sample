<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserVerification;
use App\Http\Controllers\Controller;
use League\OAuth2\Server\CryptTrait;
use App\Events\VerificationTokenGenerated;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * User Login.
 *
 * @category Login
 *
 * @author    hitesh kumar <hitesh2live@gmail.com>
 * @license  PHP License 7.1.25
 */
class LoginController extends Controller
{
    use Concerns\FindUser;
    use CryptTrait;
    use Concerns\DataVerificationToken;

    private function username()
    {
        return 'email';
    }

    /**
     * User Login.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate(
            $request, [
                $this->username() => 'required|string',
                'password' => 'required|string',
            ]
        );
        // Find user in Database
        $this->findUser($request);
        // Check Application Access
        $this->_portalAccess($request);
        // Verify the Password & throw exception if password does not match
        $this->verifyPassword($request);
        // Verified that user does have the verfied Label,
        // like if User trying to login with Email then email should be verified same as for mobile number
        $this->hasUnverifiedLabel($request);
        // Make sure that account should not be suppended.
        $this->appValidUserCondition($request);
        // Check user account status
        $this->checkAccountstatus($request);
        // Send Success Response.
        return $this->sendLoginResponse($request);
    }

    /**
     * Generate the access token from refresh token.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshToken(Request $request)
    {
        $this->validate(
            $request, [
                'refresh_token' => 'string',
            ]
        );

        $this->setEncryptionKey(app('encrypter')->getKey());

        try {
            $refresh_token = json_decode($this->decrypt($request->refresh_token));
        } catch (\Exception $e) {
            $this->setErrorCode('invalid_refresh_token');
            throw OAuthServerException::invalidRefreshToken();
        }
        $oauth_refresh_tokens = \DB::table('oauth_refresh_tokens')->where(
            'id', $refresh_token->refresh_token_id
        )->first();

        // When refresh token does not exists in database.
        if (!$oauth_refresh_tokens) {
            $this->setErrorCode('refresh_token_not_exist');
            throw OAuthServerException::invalidRefreshToken();
        }

        // Check Refresh token expire date
        if (\Carbon\Carbon::parse($oauth_refresh_tokens->expires_at)->isPast()) {
            $this->setErrorCode('refresh_token_expired');
            throw OAuthServerException::invalidRefreshToken();
        }

        if ($oauth_refresh_tokens->revoked == 1) {
            $this->setErrorCode('refresh_token_revoked');
            throw OAuthServerException::invalidRefreshToken();
        }
        $access_token = \DB::table('oauth_access_tokens')->where(
            'id', $refresh_token->access_token_id
        )->first();

        if (!$access_token) {
            $this->setErrorCode('access_token_not_exist');
            throw OAuthServerException::invalidRefreshToken();
        }

        if ($access_token->client_id != $request->client()->id) {
            $this->setErrorCode('refresh_token_invalid_client');
            throw OAuthServerException::invalidGrant();
        }

        if ($access_token->device_id != $request->device()->id) {
            $this->setErrorCode('refresh_token_invalid_device');
            throw OAuthServerException::invalidGrant();
        }
        // Revoked the refresh token.
        \DB::table('oauth_refresh_tokens')->where(
            'id',
            $refresh_token->refresh_token_id
        )->update(['revoked' => 1]);

        // Find user in Database
        $this->findUser($request, $refresh_token->user_id);
        // Make sure that account should not be suppended.
        $this->appValidUserCondition($request);

        return $this->sendLoginResponse($request);
    }

    /**
     * To verify the password.
     *
     * @param \Illuminate\Http\Request
     */
    private function verifyPassword(Request $request)
    {
        // Throw the exception if password does not match.
        if (!\Hash::check($request->password, $this->user->password)) {
            throw ValidationException::withMessages([
                'password' => [trans('Password wrong')],
            ]);
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    private function sendLoginResponse(Request $request)
    {
        /*
         * Deactivating other user from this device
         */
        $this->_deactiveOtherUser($request, $this->user->id);

        /*
         * Deactivating Other Device of this user
         */
        $this->_deactivateOtherDevice($request, $this->user->id);

        /*
         * Logout User from Other Device.
         */
        $this->_logoutFromOtherDevice($request, $this->user->id);

        //  Generate the User Access Token
        $token = $this->user->createUserToken();
        // Linked to user with respected device
        $device = $this->linkToDevice($request)->only('pivot');
        // Update device token if get in request
        if ($request->device_token) {
            $request->device()->update(['device_token' => $request->device_token]);
        }
        $permissions = $this->user->role->menuItems()->get()->pluck('name');
        $this->user->setRelation('permissions', $permissions);
        if ($this->user->role->name == 'consultant') {
            $this->user->load('consultantRate');
        }
        // send user information
        return $this->setData([
            'user' => $this->user,
            'device' => $device['pivot'],
            'token' => $token,
        ])->response();
    }

    /**
     * Logout User From other Mobile device means expire the token.
     *
     * @param $request \Illuminate\Http\Request $request
     * @param $user_id int
     */
    private function _logoutFromOtherDevice(Request $request, $user_id)
    {
        if ($request->client()->name == 'iOS' || $request->client()->name == 'Android') {
            $mobile_clients = \DB::table('oauth_clients')->whereIn(
                'name', ['iOS', 'Android']
            )->select('id')->get()->pluck('id')->toArray();

            \DB::table('oauth_access_tokens')->where(
                'user_id',
                $user_id
            )->whereIn('client_id', $mobile_clients)->delete();
        }
    }

    /**
     * WHen user login in a device then deactive to other device for same user.
     *
     * @param $request \Illuminate\Http\Request $request
     * @param $user_id int
     *
     * @return avoid
     */
    private function _deactivateOtherDevice(Request $request, $user_id)
    {
        \DB::table('device_user')->where(
            'user_id', $user_id
        )->where('device_id', '<>', $request->device()->id)->update(
            [
                'active' => 'No',
                'revoked' => '1',
            ]
        );
    }

    /**
     * When user is login then De-active current device for other user.
     *
     * @param $request \Illuminate\Http\Request $request
     * @param $user_id int
     *
     * @return avoid
     */
    private function _deactiveOtherUser(Request $request, $user_id)
    {
        \DB::table('device_user')->where(
            'device_id', $request->device()->id
        )->where('user_id', '<>', $user_id)->update(
            [
                'active' => 'No',
                'revoked' => '1',
            ]
        );
    }

    /**
     * Update the device and user relation.
     */
    private function linkToDevice($request)
    {
        $user_device = $this->user->devices()->where('devices.id', $request->device()->id)->first();
        $max_login_index = $request->device()->users()->withTrashed()->max('login_index');
        $max_login_index = $max_login_index !== null ? $max_login_index + 1 : 0;
        $time_offset = $request->header('time-offset');
        $this->user->update(['timezone' => $time_offset]);

        if (!$user_device) {
            $request->device()->users()->syncWithoutDetaching(
                [
                    $this->user->id => [
                        'login_index' => $max_login_index,
                        'settings' => [
                                'allow_push_notification' => 'Yes',
                            ],
                        'timezone' => $time_offset,
                    ],
                ]
            );
        } else {
            $request->device()->users()->syncWithoutDetaching(
                [
                    $this->user->id => [
                        'active' => 'Yes',
                        'revoked' => '0',
                        'timezone' => $time_offset,
                    ],
                ]
            );
        }

        return $this->user->devices()->where(
            'devices.id', $request->device()->id
        )->first();
    }

    /**
     * verify email or mobile number.
     */
    public function generateToken(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required',
        ]);
        $user = $this->findUser($request);
        // Make sure that account should not be suppended.
        $this->appValidUserCondition($request);
        $forgot_password_by = $this->getLoginLabel($request);
        $token = ($forgot_password_by == 'email') ? \Str::random(6) : rand(100000, 999999);
        $user_verification = UserVerification::create([
            'email' => ($forgot_password_by == 'email') ? $request->{$this->username()} : null,
            'mobile_no' => ($forgot_password_by != 'email') ? $request->{$this->username()} : null,
            'user_id' => $user->id,
            'token' => $token,
            'for' => ($forgot_password_by == 'email') ? 'email_verification' : 'mobile_verification',
        ]);
        event(new VerificationTokenGenerated($forgot_password_by,
            $user_verification, $user));

        return $this->setMessage('Reset link and token are successfully send.')
            ->response();
    }

    public function verifyToken(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
        ]);
        $token_data = $this->checkValidToken($request);

        return $this->setMessage('Token is valid')
            ->response();
    }

    /**
     * To verify the mobile number Or Email Address.
     */
    public function userEmailOrMobileVerify(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
        ]);
        $token_data = $this->checkValidToken($request);
        $user = User::find($token_data->user_id);
        $label = ($token_data->for == 'email_verification') ? 'email' : 'mobile number';
        if ($token_data->for == 'email_verification') {
            if ($user->email_verification == 'Yes') {
                $this->setMessage('Your email id already verified')
                    ->setErrorCode('email_already_verified');

                throw ValidationException::withMessages([]);
            }
            $user->email_verification = 'Yes';
        } else {
            if ($user->mobile_verification == 'Yes') {
                $this->setMessage('Your mobile number already verified')
                    ->setErrorCode('mobile_already_verified');

                throw ValidationException::withMessages([]);
            }
            $user->mobile_verification = 'Yes';
        }

        $user->save();
        $token_data->delete();

        return $this->setMessage('Your '.$label.' are successfully verified.')
            ->response();
    }
}
