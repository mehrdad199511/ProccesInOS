<?php

namespace App\Http\Controllers\Api\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ApiOperationsException;
use App\Http\Controllers\Api\Log\LogController;
use stdClass;

class GetDirectoriesListController extends Controller
{
    /**
     * constant value for get processes
     * 
     */
    private const PARENT_DIRECTORY = '/opt/myprogram/';


    /**
     * Store user access token
     *
     * @var array
     */
    private $_output = [];


    /**
     * store username
     *
     * @var string
     */
    private $_username = '';

    
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
    public function getDirectories(Request $request, LogController $logController)
    {   
        $this->_username = auth()->user()->name;

        $log = [
            'request' => json_encode(
                'name : ' . $this->_username
            ),
        ];

        try {
            $command = 'cd ' . self::PARENT_DIRECTORY . $this->_username . ' && ls -d */';
    
            $this->_checkParentDirectoryExist();
            $this->_checkUserDirectoryExist();

            exec($command, $this->_output, $this->_resultCode);
            $this->_checkCommandResult();
    
            $successResponse = [
                'status' => true,
                'message' => 'Receive user directories list successfully.',
                'result' => $this->_output,
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
     * Check parent directory exist
     *
     * @return bool|Exception
     * @throws ApiOperationsException
     */
    private function _checkParentDirectoryExist()
    {                
        if(!file_exists(self::PARENT_DIRECTORY)) {

            throw new ApiOperationsException(
                'The operation is not execute. parent directory not exist !',
                500
            );
        }

        return true;
    }


    /**
     * Check user directory exist
     *
     * @return bool|Exception
     * @throws ApiOperationsException
     */
    private function _checkUserDirectoryExist()
    {                
        if(!file_exists(self::PARENT_DIRECTORY . '/' . $this->_username)) {

            throw new ApiOperationsException(
                'The operation is not execute. user directory not exist !'
            );
        }

        return true;
    }



    /**
     * Check command execute result
     *
     * @return array|Exception
     * @throws ApiOperationsException
     */
    private function _checkCommandResult()
    {                
        if ($this->_resultCode == 2) {

            throw new ApiOperationsException(
                'There is no directory for this user !',
                422
            );
        }

        if ($this->_resultCode !== 0) {

            throw new ApiOperationsException(
                ' The operation is not executable. The result code is : '.
                $this->_resultCode 
                .'. Please report the operator.',
                422
            );
        }

        $operationResult = [];

        foreach ($this->_output as $value) {

            $operationResult[] = str_replace("/", '', $value);
        }

        $this->_output = $operationResult;

        return true;
    }
}
