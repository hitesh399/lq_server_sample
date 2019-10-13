<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class RequestLogFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function setup()
    {
        $this->select([
            'request_logs.url',
            'request_logs.route_name',
            'request_logs.request_method',
            'request_logs.client_id',
            'request_logs.ip_address',
            'request_logs.device_id',
            'request_logs.user_id',
            'request_logs.response_status',
            'request_logs.status_code',
            'request_logs.request_headers',
            'request_logs.response_headers',
            'request_logs.request',
            'request_logs.response',
        ])
        ->with(['device', 'user', 'client'])
        ->orderBy('request_logs.id', 'DESC');
    }

    /**
     * To get the request log according to given keywords.
     *
     * @param $val String
     */
    public function search($val)
    {
        $this->where(function ($q) use ($val) {
            $q->orWhere('request_logs.url', 'LIKE', "%{$val}%")
                ->orWhere('request_logs.route_name', 'LIKE', "%{$val}%")
                ->orWhere('request_logs.request_method', 'LIKE', "%{$val}%")
                ->orWhere('request_logs.ip_address', 'LIKE', "%{$val}%")
                ->orWhere('request_logs.response_status', 'LIKE', "%{$val}%")
                ->orWhere('request_logs.status_code', 'LIKE', "%{$val}%")
                ->orWhere('request_logs.url', 'LIKE', "%{$val}%");
        });
    }

    /**
     * To get the request log of given user.
     *
     * @param $user_id Integer
     */
    public function user($user_id)
    {
        $this->where('request_logs.user_id', $user_id);
    }

    public function responseStatus($val)
    {
        $this->where('request_logs.response_status', $val);
    }

    /**
     * To get the request log of given device.
     *
     * @param $device_id Integer
     */
    public function device($device_id)
    {
        $this->where('request_logs.device_id', $device_id);
    }

    /**
     * To get the request log of given client.
     *
     * @param $client_id Integer
     */
    public function client($client_id)
    {
        $this->where('request_logs.client_id', $client_id);
    }
}
