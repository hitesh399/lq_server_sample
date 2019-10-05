<?php

namespace App\Http\Controllers\Api\MyProfile;

use Auth;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use App\Events\ChangedEmail;
use App\Models\UserVerification;
use App\Models\Role;
use App\Models\User;
use App\Events\ChangedMobileNumber;
use League\OAuth2\Server\CryptTrait;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Api\Auth\Concerns\FindUser;

/**
 * Add Related Photos
 *
 * @category My Profile
 * @package  My Profile
 * @author   Sachiln Kumar <sachin@singsys.com>
 * @license  PHP License 7.1.25
 * @link     
 */
class RelatedPhotoController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photos = \Auth::user()->photos()->orderBy('id', 'DESC')->lqPaginate();
        return $this->setData($photos)
            ->response();        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                'photo.file' => 'mimes:jpeg,jpg,png,gif',
                'photo' => 'required'
            ]
        );
        $user = Auth::user();
        $user->photos()->addMedia([$request->photo], 'related_image', null, false);
        return $this->setMessage('Related Image Uploaded Successfully.')
        ->response();
    }

     /**
     * Delete user related photos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($photo_id)
    { 
        $user = Auth::user();
        $photos = $user->photos()->getQuery()->where('id', $photo_id)->delete();
        return $this->setMessage('Photo deleted successfully')->response();
    }
    
}
