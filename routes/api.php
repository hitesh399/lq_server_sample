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
Route::options('{name}', function () {
    return response()->json(['status' => true]);
})->where(['name' => '[a-z\/\-\_0-9]+']);

Route::get('/', function () {
    return view('welcome');
});
Route::get('calling-code', 'UniversalController@callingCode')->name('calling-code');
Route::get('site-general-config', 'UniversalController@siteConfigurations')->name('site-general-config');

Route::post('register', 'Auth\RegisterController@index')->name('register');
Route::post('login', 'Auth\LoginController@index')->name('login');
Route::get('my-profile', 'Auth\MyProfileController@index')->name('my-profile');
Route::patch('logout', 'Auth\MyProfileController@logout')->name('logout');
Route::post('generate-token', 'Auth\LoginController@refreshToken')->name('generate-token');

/*
 * Device APi.
 */
Route::get('device/user', 'DeviceController@deviceLoginUser')->name('device-user-list');
Route::patch('device/user/{id}/revoke', 'DeviceController@revokedDeviceUser')->name('device-user-revoke');

Route::apiResource('media', 'Media\MediaController');
Route::post('media-token', 'Media\MediaTokenController@store');
