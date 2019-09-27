<?php
use Illuminate\Http\UploadedFile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $file = [];
    // $file['file'] = UploadedFile::fake()->image('avatar.jpg', 600, 600);
    // $u = \App\Models\User::first();
    // // // $u->profileImage()->addMedia($file);
    // // $u->photos()->addMedia([$file, $file], 'test', null);
    // // $u1 = \DB::table('users')->first();
    // $u->lqUpdate(['name' => 'Hitesh Kumar']);
    // dd($u);
    // dd($u->lqPaginate(['*'], true));
});
