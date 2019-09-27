<?php

namespace App\Http\Controllers\Api\Media;

use App\Models\MediaToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Create new Media to upload the file.
 *
 * @category Media
 *
 * @author Hitesh Kumar <live2hitesh@gmail.com>
 */
class MediaTokenController extends Controller
{
    /**
     * To create new media token.
     *
     * @param \Illuminate\Http\Request $request collection of request data
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:800',
                'size' => 'required|int',
            ]
        );
        $media_token = MediaToken::create(
            [
                'file_name' => $request->name,
                'file_size' => $request->size,
                'path' => $request->path ? $request->path : 'uploads',
                'token' => Str::random(200),
                'device_id' => $request->device()->id,
                'client_id' => $request->client()->id,
            ]
        );

        return $this->setData(
            [
                'media_token' => $media_token,
            ]
        )->response();
    }
}
