<?php

namespace App\Http\Controllers\Api\Auth\Concerns;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * To find the user from the request input.
 *
 * @category Auth
 *
 * @author  Hitesh Kumar <live2hitesh@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @see https://github.com/hitesh399
 */
trait FindUser
{
    private $_user = null;
    private $_currentRole = null;

    /**
     * Get the request Key name, in which user information will contain.
     *
     * @return string
     */
    private function username(): string
    {
        return 'email';
    }

    private function _setPermission(User $user, $role_id = null)
    {
        $roles = $user->roles;
        if ($role_id) {
            $roles = $roles->where('id', $role_id);
        }
        $permissions = $roles->map(
            function ($q) {
                return $q->menuItems->pluck('name');
            }
        );

        $user->setRelation('permissions', $permissions->flatten());
    }

    /**
     * To Get User Profile.
     *
     * @param \Illuminate\Http\Request $request Class contains all request data
     * @param \App\Models\User         $user    User Model
     *
     * @return Illuminate\Support\Collection
     */
    private function findUser(Request $request, User $user = null): User
    {
        /*
         * If already fetched the user detail
         */
        if ($this->_user) {
            return $this->_user;
        }
        if (!$user) {
            $this->_user = User::where(
                function ($q) use ($request) {
                    $q->orwhere('email', $request->{$this->username()})
                        ->orWhere('mobile_no', $request->{$this->username()});
                }
            )->withTrashed()->first();
        } else {
            $this->_user = $user;
        }
        /*
         * If user does not exists in  database.
         */
        if (!$this->_user) {
            throw ValidationException::withMessages(
                [
                    $this->username() => [trans('User does not exist')],
                ]
            );
        }
        $this->_user->load(
            ['roles.menuItems', 'profileImage']
        );
        $this->_user->setAppends(['role_access_type']);
        $this->_onlyAppRoles($request, $this->_user);

        return $this->_user;
    }

    /**
     * Finding the label,
     * which is using by user to the login attamp like: email,mobile_no,password.
     *
     * @param \Illuminate\Http\Request $request Class contains all request data
     *
     * @return string
     */
    private function _getLoginLabel(Request $request): string
    {
        if (filter_var($request->{$this->username()}, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif (preg_match('/^(\+|[0-9]){1,3}(\-)([0-9]){6,12}$/', $request->{$this->username()})) {
            return 'mobile_no';
        }
    }

    /**
     * Finding the label, which is using by user
     * to the login attamp like: email,mobile_no,password.
     *
     * @param \Illuminate\Http\Request $request Class contains all request data
     *
     * @return throw ValidationException | void
     */
    private function _checkAccountstatus(Request $request): void
    {
        $user = User::where('email', $request->{$this->username()})
            ->orWhere('mobile_no', $request->{$this->username()})
            ->first();
        if ($user->status === 'inactive') {
            $this->setErrorCode('account_in_deactivated_mode');
            throw ValidationException::withMessages(
                [
                    $this->username() => [
                        trans(
                            'Your account is inactive mode please contact to your Admin or wait sometime.'
                        ),
                    ],
                ]
            );
        }
    }

    /**
     * Check if user tring to login with unverified email or contact number.
     *
     * @param \Illuminate\Http\Request $request Class contains all request data
     *
     * @return throw ValidationException | void
     **/
    protected function hasUnverifiedLabel(Request $request): void
    {
        $label = $this->_getLoginLabel($request);
        $user = $this->_user;
        if ($label == 'email' && !$user->email_verified_at) {
            $this->setErrorCode('email_not_verified');
            throw ValidationException::withMessages(
                [
                    $this->username() => [trans('auth.email_not_verified')],
                ]
            );
        } elseif ($label == 'mobile_no' && !$user->mobile_no_verified_at) {
            $this->setErrorCode('mobile_not_verified');
            throw ValidationException::withMessages(
                [
                    $this->username() => [trans('auth.mobile_not_verified')],
                ]
            );
        }
    }

    /**
     * Verify the Application valid user conditions.
     *
     * @param \Illuminate\Http\Request $request Class contains all request data
     *
     * @return throw ValidationException | void
     */
    private function _appValidUserCondition(Request $request): void
    {
        $user = $this->_user;
        if ($user->deleted_at) {
            $this->setErrorCode('account_suspended');
            throw ValidationException::withMessages(
                [
                    $this->username() => [trans('Account suspended')],
                ]
            );
        }
    }

    /**
     * The user should have atleast a role,
     * which have accebility of access current application.
     *
     * @param \Illuminate\Http\Request $request Class contains all request data
     * @param \App\Models\User         $user    Class contains the user detail
     *
     * @return throw ValidationException | bool
     */
    private function _portalAccess(Request $request, User $user = null)
    {
        $this->_currentRole = $this->_user->roles->first();

        if (!$this->_currentRole) {
            $this->setErrorCode('forbidden');
            throw ValidationException::withMessages(
                [
                    $this->username() => [trans('auth.forbidden')],
                ]
            );
        }
        $role_id = $this->_user->role_access_type == 'one_at_time' ? $this->_currentRole->id : null;
        $this->_setPermission($this->_user, $role_id);

        return true;
    }

    private function _onlyAppRoles(Request $request, User $user): void
    {
        $client_id = $request->client()->id;
        $roles = $user->roles->filter(
            function ($item, $key) use ($client_id) {
                return in_array($client_id, $item->client_ids);
            }
        );

        $user->setRelation('roles', $roles);
    }
}
