<?php
namespace App\Http\Controllers\Api\Auth\Concerns;

use Illuminate\Http\Request;
use App\Models\UserVerification;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

Trait DataVerificationToken
{

    /**
     * To check the valid token
     * @param String token
     */
    protected function checkValidToken($token)
    {
        $now_reduce_mins =  \Carbon\Carbon::now()->addMinutes(-120)->toDateTimeString();
        $this->user_verification = UserVerification::where(function($q) use($token) {

            if(env('APP_ENV') != 'production') {
                $q->orWhere('token', $token->token)
                ->orWhere('mobile_no', $token->token);
            }
            else {

                $q->where('token', $token->token);
            }
        })
        ->where('created_at','>=', $now_reduce_mins)
        ->first();

        // Check token a valid token is exists or not in database

        if (!$this->user_verification) {

            $this->setMessage('Token not exist or may be expired')
                ->setErrorCode('token_not_exist_or_may_be_expired');

            throw ValidationException::withMessages([
                'token' => [trans('auth.token_not_exist_or_may_be_expired')],
            ]);
        }
        else
        {
            return $this->user_verification;

        }

    }
}

?>
