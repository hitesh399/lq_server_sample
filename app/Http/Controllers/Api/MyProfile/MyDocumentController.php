<?php

namespace App\Http\Controllers\Api\MyProfile;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ModelFilters\MyDocumentFilter;
use Illuminate\Validation\ValidationException;

class MyDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::User();
        $docs = null;
        $docs = $user->qualifications()->get();
        return $this->setData(['data' => $docs])->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::User();
        if ($request->type === 'qualifications') {
            $count = $user->qualifications()->count();
            if($count >= 5) {
                $this->setMessage('You can not add more then five qualifications')
                    ->setErrorCode('qualification_limit_exceeded');
                throw ValidationException::withMessages([
                    'token' => [trans('auth.qualification_limit_exceeded')],
            ]);
            } 
            $user->qualifications()->addMedia(
                [$request->qualification], 
                'qualifications',
                null,
                false
            );           
        } else {
            $user->idProof()->addMedia($request->id_proof, 'id_proofs');
        }
        return $this->setData(['data' => $user])->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::User();
        $docs = null;
        if ($request->type === 'qualifications') {
            $docs = $user->qualifications();
        } else {
            $docs = $user->idProof();
        }
        $docs->where('id', $id)->delete();
        return $this->setMessage('Deleted Successfully')->response();
    }
}
