<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Config;

class UniversalController extends Controller
{
    /**
     * To get the Country codes.
     */
    public function callingCode(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ipdata = @json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip));

        $country_code = @$ipdata->geoplugin_countryCode ? @$ipdata->geoplugin_countryCode : 'SG';

        $data = json_decode(file_get_contents(base_path('calling_code.json')), true);

        $selected_code = array_column(collect($data)->where('country_code', $country_code)->toArray(), 'code')[0];

        return $this->setData([
                'data' => $data,
                'selected' => [
                    'code' => $selected_code,
                ],
            ])
        ->response(200);
    }

    public function siteConfigurations(Request $request)
    {
        $configs = Config::where('autoload', '1')->get(['data', 'name', 'options'])->keyBy('name');

        return $this->setData([
            'data' => $configs,
        ])
        ->response(200);
    }
}
