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

Route::apiResource(
    'notification-template',
    'NotificationTemplateController'
)->name('notification-template', null);
Route::get(
    'notification-template/email/header-footer',
    'NotificationTemplateController@emailFooterHeader'
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
