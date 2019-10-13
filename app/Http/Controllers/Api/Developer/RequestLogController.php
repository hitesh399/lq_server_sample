<?php

namespace App\Http\Controllers\Api\Developer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ModelFilters\RequestLogFilter;
use App\Models\RequestLog;

class RequestLogController extends Controller
{
    /**
     * To get the Request Log List.
     *
     * @param Illuminate\Http\Request $request [All Request]
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request_log = RequestLog::filter(
            $request->all(),
            RequestLogFilter::class
        )->lqPaginate();

        return $this->setData($request_log)
            ->response();
    }
}
