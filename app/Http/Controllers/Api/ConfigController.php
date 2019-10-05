<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Config;
use App\ModelFilters\ConfigFilter;
use Illuminate\Support\Facades\Crypt;
use Singsys\LQ\Lib\Media\MediaUploader;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $config = Config::filter(
            $request->all(), ConfigFilter::class
        )->where('config_type', 'global')->lqPaginate();
        return $this->setData($config)
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
        $config_data = $request->only(
            [
                'name',
                'config_group',
                'options'
            ]
        );
        $config = Config::create($config_data);
        return $this->setData($config)
            ->response();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $config = Config::findOrfail($id);
        return $this->setData([
            'config' =>  $config
        ])->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * Update the config field Setting
         */
        if ($request->update_config_field) {
            $config = Config::findOrFail($id);
            $config_data = $request->only(
                [
                    'name',
                    'config_group',
                    'options'
                ]
            );
            $config->update($config_data);
            return $this->setData(['config' => $config])
                ->response();

        } else {
            /**
             * Update the Config data value
             */
            return $this->_updateDataValue($request, $id);
        }
    }

    /**
     * To update the Config data Value
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id  [Config Primary Id]
     *
     * @return \Illuminate\Http\Response
     */
    private function _updateDataValue(Request $request, $id)
    {
        $config = Config::findOrfail($id);
        $modfiied_data = [];
        $modfiied_data['options'] = $config->options;
        $options = $config->options;
        if (isset($request->options['current_type'])) {
            $options['type'] = $request->options['current_type'];
            $modfiied_data['options']['current_type'] =  $request->options['current_type'];
            $config->options = $options;
        }

        if (isset($config->options['type']) && $config->options['type'] =='file') {
            $media = new MediaUploader($request->data, 'config_files');
            $data =  $media->uploadAndPrepareData();
            $data = json_encode($data);
        } else {
            $data =  isset($config->options['secure']) && $config->options['secure'] ?  Crypt::encrypt($request->data) : $request->data;
        }
        $modfiied_data['data'] =  is_array($data) ? json_encode($data) : $data;
        $config->update($modfiied_data);
        \Cache::forget('site_config.'. $config->name);
        return $this->setData([
            'config' =>  $config
        ])->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $config = Config::findOrfail($id);
        $config->delete();
        \Cache::forget('site_config.'. $config->name);

        return $this->setData([
            'config' =>  $config
        ])->response();
    }
}
