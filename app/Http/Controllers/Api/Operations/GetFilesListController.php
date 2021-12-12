<?php

namespace App\Http\Controllers\Api\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ApiOperationsException;
use App\Http\Controllers\Api\Log\LogController;

class GetFilesListController extends Controller
{
     /**
     * constant value for get processes
     * 
     */
    private const PARENT_DIRECTORY = '/opt/myprogram/';


    /**
     *  Store final operation output
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
     * Get files list from user directory 
     *
     * @param Request $request
     * @param LogController $logController
     * @return Response
     */
    public function getFiles(Request $request, LogController $logController)
    {   
        $this->_username = auth()->user()->name;

        $log = [
            'request' => json_encode(
                'name : ' . $this->_username
            ),
        ];

        try {
            $this->_checkParentDirectoryExist();
            $this->_checkUserDirectoryExist();
            $this->_checkUerDirectoryfilesList();
            
            $successResponse = [
                'status' => true,
                'message' => 'Receive files list from user directory successfully.',
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
                'The operation is not execute. user directory not exist !',
                500
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
    private function _checkUerDirectoryfilesList()
    {                

        $fileNames = [];
        $contents = scandir(self::PARENT_DIRECTORY . $this->_username);

        foreach($contents as $content) {

            if(is_file(self::PARENT_DIRECTORY . $this->_username . DIRECTORY_SEPARATOR . $content)) {
                array_push($fileNames, $content);
            }
        }       

        if(empty($fileNames)) {

            throw new ApiOperationsException(
                'There is no file for this user directory!',
                422
            );
        }

        $this->_output = $fileNames;
        return true;
    }
}