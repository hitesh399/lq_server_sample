<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use App\Http\Controllers\Controller;
use League\OAuth2\Server\CryptTrait;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;

class LoginController extends Controller
{
    use Concerns\FindUser, CryptTrait;
    //
    public function index(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string'
        ]);
        # Find user in Database
        $this->findUser($request);
        # Verify the Password & throw exception if password does not match
        $this->verifyPassword($request);
        # Verified that user does have the verfied Label,
        # like if User trying to login with Email then email should be verified same as for mobile nuber
        $this->hasUnverifiedLabel($request);
        # Make sure that account should not be suppended.
        $this->appValidUserCondition($request);
        # Send Success Response.
        return $this->sendLoginResponse($request);
    }
    /**
     * Generate the access token from refresh token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refreshToken(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'string',
        ]);

        $this->setEncryptionKey(app('encrypter')->getKey());

        try {
            $refresh_token = json_decode($this->decrypt($request->refresh_token));
        } catch (\Exception $e) {
            $this->setErrorCode('invalid_refresh_token');
            throw OAuthServerException::invalidRefreshToken();
        }
        $oauth_refresh_tokens = \DB::table('oauth_refresh_tokens')->where('id', $refresh_token->refresh_token_id)->first();

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
        $access_token = \DB::table('oauth_access_tokens')->where('id', $refresh_token->access_token_id)->first();

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
        \DB::table('oauth_refresh_tokens')->where('id', $refresh_token->refresh_token_id)->update([
            'revoked' => 1
        ]);

        # Find user in Database
        $this->findUser($request, $refresh_token->user_id);
        # Make sure that account should not be suppended.
        $this->appValidUserCondition($request);

        return $this->sendLoginResponse($request);
    }
    /**
     * To verify the password
     * @param \Illuminate\Http\Request
     */
    private function verifyPassword(Request $request)
    {
        # Throw the exception if password does not match.
        if (!\Hash::check($request->password, $this->user->password)) {
            throw ValidationException::withMessages([
                'password' => [trans('auth.password_wrong')],
            ]);
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function sendLoginResponse(Request $request)
    {
        # Generate the User Access Token
        $token = $this->user->createUserToken();
        # Linked to user with respected device
        $device = $this->linkToDevice($request)->only('pivot');
        # Update device token if get in request
        if ($request->device_token) {
            $request->device()->update(['device_token' => $request->device_token]);
        }
        # send user information
        return $this->setData([
            'user' => $this->user,
            'device' => $device['pivot'],
            'token' => $token
        ])
        ->response();
    }

    /**
     * Update the device and user relation.
     */
    private function linkToDevice($request)
    {
        $user_device = $this->user->devices()->where('devices.id', $request->device()->id)->first();
        $max_login_index = $request->device()->users()->withTrashed()->max('login_index');
        $max_login_index = $max_login_index !== null ? $max_login_index +1 : 0;

        if (!$user_device) {
            $request->device()->users()->syncWithoutDetaching([$this->user->id =>
                [
                'login_index' => $max_login_index,
                'settings' => [
                        'allow_push_notification' => 'Yes',
                    ]
                ]
            ]);
        } else {
            $request->device()->users()->syncWithoutDetaching([$this->user->id => ['active' => 'Yes', 'revoked' => '0']]);
        }
        return $this->user->devices()->where('devices.id', $request->device()->id)->first();
    }
}
