<?php

namespace App\Http\Controllers\Api\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\CreateDirectoryRequest;
use App\Http\Controllers\Api\Log\LogController;
use App\Exceptions\ApiOperationsException;
use Illuminate\Support\Env;


class CreateDirectoryController extends Controller
{             

    /**
     * store username
     *
     * @var string
     */
    private $_username = '';


    /**
     * Store user custom directory
     *
     * @var string
     */
    private $_customDirectory = '';



    /**
     * Execute user request for create directory
     *
     * @param Request $request
     * @param LogController $logController
     * @return Response
     */
    public function create(CreateDirectoryRequest $request, LogController $logController) {

        $this->_username = auth()->user()->name;
        $this->_customDirectory = $request->title;
        $secureDirecrotyTitle =  escapeshellcmd($this->_customDirectory);

        $log = [
            'request' => json_encode($request->all()),
        ];

        try {

            $this->_createParentDirectory();
            $this->_createUserDirectory();
            $this->_createCustomDirectory();

            $successResponse = [
                'status' => true,
                'message' => 'Your directory create successfully.',
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
     * Get parent directory name from .env
     * file and create parent directory
     * 
     * Below changes is require for create parent directory :
     * 
     * chown -r www-data:www-data /opt
     * chmod -R 777 /opt
     *
     * @return bool|Exception
     * @throws ApiOperationsException
     */
    private function _createParentDirectory()
    {                
        if(!file_exists(env('PARENT_DIRECTORY'))) {

            $result = mkdir(env('PARENT_DIRECTORY'), 0777, true);

            if(!$result) {

                throw new ApiOperationsException(
                    'The operation is not execute and not create parent directory !',
                    500
                );
            }
        }

        return true;
    }


    /**
     * Check user directory Exist and 
     * create username directory
     *
     * @return bool|Exception
     * @throws ApiOperationsException
     */
    private function _createUserDirectory()
    {                
        if(!file_exists(env('PARENT_DIRECTORY') . '/' . $this->_username)) {

            $result = mkdir(env('PARENT_DIRECTORY') . '/' . $this->_username, 0775, true);

            if(!$result) {

                throw new ApiOperationsException(
                    'The operation is not execute and not create username directory !',
                    500
                );
            }
        }

        return true;
    }


    /**
     * Check custom directory exist 
     * and create custom directory
     *
     * @return bool|Exception
     * @throws ApiOperationsException
     */
    private function _createCustomDirectory()
    {    
        $secureDirecrotyTitle =  escapeshellcmd($this->_customDirectory);
        // dd((env('PARENT_DIRECTORY') . '/' . $this->_username . '/' . $secureDirecrotyTitle));
        if(file_exists(env('PARENT_DIRECTORY') . '/' . $this->_username . '/' . $secureDirecrotyTitle)) {

            throw new ApiOperationsException(
                'This imported directory has already been created.',
                400
            );

        }else {

            $result = mkdir(env('PARENT_DIRECTORY') . '/' . $this->_username . '/' . $secureDirecrotyTitle, 0775, true);

            if(!$result) {

                throw new ApiOperationsException(
                    'The operation is not execute and not create your directory !',
                    500
                );
            }

            return true;
        }
    }
}