<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::options(
    '{name}', function () {
        return response()->json(['status' => true]);
    }
)->where(['name' => '[a-z\/\-\_0-9]+']);

Route::get('calling-code', 'UniversalController@callingCode')->name('calling-code');
Route::get(
    'site-general-config',
    'UniversalController@siteConfigurations'
)->name('site-general-config');

Route::post('register', 'Auth\RegisterController@index')->name('register');
Route::post('login', 'Auth\LoginController@index')->name('login');
Route::post(
    'forget-password',
    'Auth\ForgotPasswordController@forgetPassword'
)->name('forget-password');
Route::post(
    'forget-password/reset',
    'Auth\ForgotPasswordController@newPassword'
)->name('create-new-password');

Route::get('my-profile', 'Auth\MyProfileController@index')->name('my-profile.show');
Route::put(
    'my-profile',
    'MyProfile\MyProfileController@update'
)->name('my-profile.update');
Route::get(
    'my-profile/status',
    'MyProfile\MyProfileController@myProfileStatus'
)->name('profile-status');
Route::put(
    'my-profile/reset-password',
    'MyProfile\MyProfileController@resetPassword'
)->name('my-profile.reset-password');
Route::post(
    'profile-photo',
    'MyProfile\MyProfileController@myProfilePhoto'
)->name('my-profile.update-profile-photo');

Route::post('logout', 'Auth\MyProfileController@logout')->name('logout');

Route::post(
    'check-valid-token',
    'Auth\LoginController@verifyToken'
)->name('check-valid-token');
Route::post(
    'user-email-or-mobile-verify',
    'Auth\LoginController@userEmailOrMobileVerify'
)->name('email-mobile-verify');

Route::post(
    'generate-token',
    'Auth\LoginController@refreshToken'
)->name('generate-token');

/*
 * Device APi.
 */
Route::get(
    'device/user',
    'DeviceController@deviceLoginUser'
)->name('device-user-list');
Route::patch(
    'device/user/{id}/revoke',
    'DeviceController@revokedDeviceUser'
)->name('device-user-revoke');
Route::get(
    'device/role/{id}/switch', 'DeviceController@switchRole'
)->name('device-role-switch');

Route::apiResource(
    'notification-template',
    'NotificationTemplateController'
)->name('notification-template', null);
Route::get(
    'notification-template/email/header-footer',
    'NotificationTemplateController@emailBody'
)->name('notification-template.header_footer');

Route::apiResource('config', 'ConfigController')->name('config', null);

Route::get(
    'developer/request-log',
    'Developer\RequestLogController@index'
)->name('developer.request-log');

Route::post(
    'developer/execute-laravel-command',
    'Developer\DeveloperController@executeLaravelCommand'
)->name('developer.execute-laravel-command');

Route::apiResource(
    'application-menu',
    'ApplicationMenu\ApplicationMenuController'
)->name('application-menu', null);
Route::apiResource(
    'application-menu-item',
    'ApplicationMenu\ApplicationMenuItemController'
)->name('application-menu-item', null);
Route::put(
    'application-menu-item/re-arrange/order',
    'ApplicationMenu\ApplicationMenuItemController@reArrange'
)->name('application-menu-item.re-order');

Route::apiResource('media', 'Media\MediaController');
Route::post('media-token', 'Media\MediaTokenController@store');
/*
 * Notification API
 */
Route::get(
    'notification',
    'MyNotificationController@index'
)->name('mynotification.index');

Route::patch(
    'notification/{id?}',
    'MyNotificationController@read'
)->name('mynotification.read');
Route::delete(
    'notification/{id?}',
    'MyNotificationController@delete'
)->name('mynotification.delete');

Route::get(
    'notification/total-unread',
    'MyNotificationController@unRead'
)->name('mynotification.total-unread');
Route::put(
    'notification/enable-disable',
    'UserDeviceController@changeStatusNotification'
)->name('mynotification.push-notification-setting');
Route::put(
    'notification/enable-disable/mail',
    'UserDeviceController@changeMailNotificationStatus'
)->name('mynotification.email-notification-setting');

Route::get(
    'my-device',
    'UserDeviceController@deviceInfo'
)->name('my-device.info');
Route::apiResource('role', 'RoleController')->name('role', null);
Route::apiResource('permission', 'PermissionController')->name('permission', null);
Route::apiResource(
    'permission-group', 'PermissionGroupController'
)->only(['index'])->name('permission-group', null);

Route::apiResource(
    'oauth-client',
    'OauthClientController'
)->only(
    [
        'index',
    ]
)->name('oauth-client', null);
Route::apiResource('admin', 'User\AdminController')->name('admin', null);
Route::post(
    'admin/{id}/change-status',
    'User\AdminController@userChangeStatus'
)->name('admin.change-status');
