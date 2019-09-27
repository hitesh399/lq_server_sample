<?php

namespace App\Http\Controllers\Api\Media;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Singsys\LQ\Lib\Media\MediaUploader;
use App\Models\MediaToken;
use App\Models\Media;

/**
 * To handle the Media like store, update, delete, list, show.
 *
 * @category Media
 *
 * @author Hitesh Kumar <live2hitesh@gmail.com>
 */
class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request collection of request data
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request collection of request data
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $media_token = MediaToken::where('token', $request->token)->first();
        $uploader = new MediaUploader($request->file, $media_token->path);
        $media = $uploader->storeInDB();

        return $this->setData(
            [
                'media' => $media->toArray(),
            ]
        )->response();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id Media Primary id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $headers = [
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'cache-control, x-requested-with, time-offset, Content-Type, X-Auth-Token, Origin, client-id, device-id, authorization',
        ];
        if (env('APP_ENV') != 'production') {
            $headers['Access-Control-Allow-Origin'] = '*';
        } else {
            $headers['Access-Control-Allow-Origin'] = env('AllowOrigin');
        }

        $media = Media::findOrfail($id);

        if (!\Storage::exists($media->path)) {
            abort(404);
        } else {
            return \Storage::download(
                $media->path, $media->info['original_name'], $headers
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request collection of request data
     * @param int                      $id      Media Primary id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id Media Primary id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        $media->delete();
        \Storage::delete($media->getOriginal('path'));

        return $this->setMessage('File has been Deleted.')
            ->response();
    }
}
