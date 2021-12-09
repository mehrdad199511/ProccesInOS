<?php

namespace App\Http\Controllers\Api\v1\Log;

use App\Http\Controllers\Controller;
use App\Repositories\LogRepository;

/**
 * Class LogController
 * @tag Log
 * @namespace App\Http\Controllers\Api\v1\Log
 */
class LogController extends Controller
{
    /**
     * Logger
     *
     * @param $request
     * @param array $logMetaData
     * @return void
     */
    public function logger($request, array $logMetaData)
    {
        $log = [
            'ip' => $request->ip(),
            'verb' => $request->method(),
            'endpoint' => $request->getRequestUri(),
            'user_id' => (isset($logMetaData['user_id']) ? $logMetaData['user_id'] : null),
            'request' => $logMetaData['request'],
            'response' => $logMetaData['response'],
            'code' => $logMetaData['code'],
        ];

        (new LogRepository())->create($log);
    }
}
