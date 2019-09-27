<?php

namespace App\Lib;

class MyApp
{
    public static function isJson($string, $return_data = false)
    {
        $data = json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : true) : false;
    }
    /**
     * To covert the Array value in integer
     */
    public static function arrayValToInt($value)
    {
        $data = [];
        if (is_array($value)) {
            foreach ($value as $val) {
                if ($val && !in_array((int)$val, $data)) {
                    $data[] = (int)$val;
                }
            }
            return count($data) ? $data : null;
        }
        return $value;
    }
    /**
     * To Get the user device token Model
     */
    public static function getUserDeviceToken($user_id)
    {
        $user_id  = is_array($user_id) ? $user_id : [$user_id];
        return \DB::table('device_user')
            ->join('oauth_access_tokens', 'device_user.user_id', '=', 'oauth_access_tokens.user_id')
            ->join('devices', 'devices.id', '=', 'device_user.device_id')
            ->whereIn('device_user.user_id', $user_id)
            ->where('oauth_access_tokens.revoked', '0')
            ->where('devices.device_token', '<>', '')
            ->groupBy(['device_user.device_id'])
            ->select(['devices.device_token', 'device_user.settings']);
    }
}
