<?php

namespace App\Http\Controllers\Api\Auth\Concerns;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * To find the user from the request input.
 */
trait FindUser
{
    private $user = null;

    /**
     * Get the request Key name, in which user information will contain.
     */
    private function username()
    {
        return 'email';
    }

    /**
     * Find the user in Database.
     */
    private function findUser(Request $request, $user_id = null)
    {
        /*
         * If already fetched the user detail
         */
        if ($this->user) {
            return $this->user;
        }
        if (!$user_id) {
            $this->user = User::where(function ($q) use ($request) {
                $q->orwhere('email', $request->{$this->username()})
                ->orWhere('mobile_no', $request->{$this->username()});
            });
        } else {
            $this->user = User::where('id', $user_id);
        }
        $this->user = $this->user->with('role')->with(['profileImage'])->withTrashed()->first();

        /*
         * If user does not exists in  database.
         */
        if (!$this->user) {
            throw ValidationException::withMessages([
                $this->username() => [trans('User does not exist')],
            ]);
        }

        return $this->user;
    }

    /**
     * Finding the label, which is using by user to the login attamp like: email,mobile_no,password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    private function getLoginLabel(Request $request)
    {
        if (filter_var($request->{$this->username()}, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif (preg_match('/^(\+|[0-9]){1,3}(\-)([0-9]){6,12}$/', $request->{$this->username()})) {
            return 'mobile_no';
        }
    }

    /**
     * Finding the label, which is using by user to the login attamp like: email,mobile_no,password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    private function checkAccountstatus(Request $request)
    {
        $user = User::where('email', $request->{$this->username()})
            ->orWhere('mobile_no', $request->{$this->username()})
            ->first();
        if ($user->status === 'inactive') {
            $this->setErrorCode('account_in_deactivated_mode');
            throw ValidationException::withMessages([
                    $this->username() => [trans('Your account is inactive mode please contact to your Admin or wait sometime.')],
                ]);
        }
    }

    /**
     * Check if user tring to login with unverified email or contact number.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Throw Exception
     **/
    protected function hasUnverifiedLabel(Request $request)
    {
        $label = $this->getLoginLabel($request);
        $user = $this->user;
        if ($label == 'email' && !$user->email_verified_at) {
            $this->setErrorCode('email_not_verified');
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.email_not_verified')],
            ]);
        } elseif ($label == 'mobile_no' && !$user->mobile_no_verified_at) {
            $this->setErrorCode('mobile_not_verified');
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.mobile_not_verified')],
            ]);
        }
    }

    /**
     * Verify the Application valid user conditions.
     */
    private function appValidUserCondition(Request $request)
    {
        $user = $this->user;
        if ($user->deleted_at) {
            $this->setErrorCode('account_suspended');
            throw ValidationException::withMessages([
                $this->username() => [trans('Account suspended')],
            ]);
        }
    }

    /**
     * To check the User is hitting the api from valid application.
     *
     * @param $request Illuminate\Http\Request
     *
     * @return throw ValidationException
     * @return bool
     */
    private function _portalAccess(Request $request, $user = null)
    {
        $user = $user ? $user : $this->user;
        /**
         * Every user should have role and role should also have the access of current application.
         */
        $client_id = $request->client()->id;
        $client_has_portal_access = in_array(
            $client_id, ($user->role->client_ids ? $user->role->client_ids : [])
        );
        if (!$client_has_portal_access) {
            $this->setErrorCode('forbidden');
            throw ValidationException::withMessages(
                [
                    $this->username() => [trans('auth.forbidden')],
                ]
            );
        }

        return true;
    }
}
