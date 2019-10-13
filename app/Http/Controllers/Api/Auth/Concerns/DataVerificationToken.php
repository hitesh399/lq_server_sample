<?php

namespace App\Http\Controllers\Api\Auth\Concerns;

use App\Models\UserVerification;
use Illuminate\Validation\ValidationException;

/**
 * To verify the token or OTP.
 *
 * @category Auth
 *
 * @author  Hitesh Kumar <live2hitesh@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @see https://github.com/hitesh399
 */
trait DataVerificationToken
{
    /**
     * To check the valid token.
     *
     * @param string $token [OTP or token]
     *
     * @return \App\Models\UserVerification
     */
    protected function checkValidToken($token)
    {
        $now_reduce_mins = \Carbon\Carbon::now()
            ->addMinutes(-120)->toDateTimeString();

        $this->user_verification = UserVerification::where(
            function ($q) use ($token) {
                if (env('APP_ENV') != 'production') {
                    $q->orWhere('token', $token->token)
                        ->orWhere('mobile_no', $token->token);
                } else {
                    $q->where('token', $token->token);
                }
            }
        )->where('created_at', '>=', $now_reduce_mins)->first();

        // Check token a valid token is exists or not in database

        if (!$this->user_verification) {
            $this->setMessage('Token not exist or may be expired')
                ->setErrorCode('token_not_exist_or_may_be_expired');

            throw ValidationException::withMessages(
                [
                    'token' => [trans('auth.token_not_exist_or_may_be_expired')],
                ]
            );
        } else {
            return $this->user_verification;
        }
    }
}
