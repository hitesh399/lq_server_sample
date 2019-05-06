<?php

namespace App\Http\Controllers\Api\Auth\Concerns;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * To find the user from the request input
 */
trait FindUser
{
    private $user = null;
    /**
     * Get the request Key name, in which user information will contain.
     */
    private function username() {
        return 'email';
    }
    /**
     * Find the user in Database
     */
    private function findUser(Request $request, $user_id = null) {
        /**
         * If already fetched the user detail
         */
        if ($this->user) {
            return $this->user;
        }
        if (!$user_id) {
            $this->user = User::where(function ($q) use($request) {
                $q->orwhere('email', $request->{$this->username()})
                ->orWhere('mobile_no', $request->{$this->username()});

            });
        } else {

            $this->user = User::where('id', $user_id);
        }
        $this->user = $this->user->with('role')->withTrashed()->first();

        /**
         * If user does not exists in  database.
         */
        if (!$this->user) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.user_not_exist')],
            ]);
        }
        return $this->user;
    }
    /**
    * Finding the label, which is using by user to the login attamp like: email,mobile_no,password
    * @param  \Illuminate\Http\Request  $request
    * @return String
    */
    private function getLoginLabel(Request $request) {
        if (filter_var($request->{$this->username()}, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        else if (preg_match('/^(\+|[0-9]){1,3}(\-)([0-9]){6,12}$/', $request->{$this->username()})) {
            return 'mobile_no';
        }
        else {
            return 'user_name';
        }
    }
    /**
    * Check if user tring to login with unverified email or mobile.
    * @param  \Illuminate\Http\Request  $request
    * @return Throw Exception
    **/
    private function hasUnverifiedLabel(Request $request) {
        $label = $this->getLoginLabel($request);
        $user = $this->user;

        if ($label =='email' && !$user->email_verified_at){
            $this->setErrorCode('email_not_verified');
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.email_not_verified')],
            ]);
        }
        else if ($label =='mobile_no' && !$user->mobile_verified_at ){
            $this->setErrorCode('mobile_not_verified');
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.mobile_not_verified')],
            ]);
        }
    }
     /**
     * Verify the Application valid user conditions
     */
    private function appValidUserCondition(Request $request) {

        $user = $this->user;
        if ($user->deleted_at) {
            $this->setErrorCode('account_suspended');
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.account_suspended')],
            ]);
        }
    }
}

