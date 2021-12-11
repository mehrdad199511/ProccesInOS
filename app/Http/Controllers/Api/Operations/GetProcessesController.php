<?php

namespace App\Http\Controllers\Api\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ApiOperationsException;
use App\Http\Controllers\Api\Log\LogController;
use Dotenv\Result\Success;
use stdClass;

class GetProcessesController extends Controller
{
     
    /**
     * constant value for get processes
     * 
     */
    private const PROCESSES_COMMAND = 'ps -A';


    /**
     * Store user access token
     *
     * @var array
     */
    private $_output = [];


    /**
     * Store command exeute result code 
     *
     * @var int
     */
    private $_resultCode = null;


    /**
     * Get processes from server
     *
     * @param Request $request
     * @param LogController $logController
     * @return Response
     */
    public function getProcesses(Request $request, LogController $logController)
    {   
 
        $log = [
            'request' => json_encode($request->all()),
        ];

        try {

            exec(self::PROCESSES_COMMAND, $this->_output, $this->_resultCode);
            $operationResult = $this->_checkProcessCommand();
    
            $successResponse = [
                'status' => true,
                'message' => 'Receive processes successful.',
                'result' => $operationResult,
            ];
            
            $log['response'] = json_encode($successResponse);
            $log['code'] = 200;
            
            return response()->json($successResponse, 200);
    
            
        } catch (ApiOperationsException $e) {

            $e->customReport($e);
            
            $log['response'] = $e->getMessage();
            $log['code'] = $e->getCode();
            
            return response(['status' => false, 'message' => $e->getMessage()], $e->getCode());

        }finally {
    
            $logController->logger($request, $log);
        }
    }


    /**
     * Check command execute result
     *
     * @return array|Exception
     * @throws ApiOperationsException
     */
    private function _checkProcessCommand()
    {                
        if ($this->_resultCode !== 0) {

            throw new ApiOperationsException(
                ' The operation is not executable. The result code is : '.
                $this->_resultCode 
                .'. Please report the operator.',
                422
            );
        }

        $operationResult = [];
            
        for ($i=1; $i <= count($this->_output)-1; $i++) {

            $object = new stdClass();

            $trimOutput    = trim($this->_output[$i]);
            $replaceOutput = preg_replace('/\s+/', " ", $trimOutput);
            $explodeOutput = explode(" ",$replaceOutput);

            $object->PID  = $explodeOutput[0];
            $object->TTY  = $explodeOutput[1];
            $object->TIME = $explodeOutput[2];
            $object->CMD  = $explodeOutput[3];
            
            $operationResult[]= $object;
        }

        return $operationResult;
    }
}
