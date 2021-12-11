<?php

namespace App\Http\Controllers\Api\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\CreateFileRequest;
use App\Http\Controllers\Api\Log\LogController;
use App\Exceptions\ApiOperationsException;

class CreateFileController extends Controller
{
    /**
     * constant value for parent directory
     * 
     */
    private const PARENT_DIRECTORY = '/opt/myprogram';
    

    /**
     * store username
     *
     * @var string
     */
    private $_username = '';


    /**
     * Store user custom file title
     *
     * @var string
     */
    private $_customFileTitle = '';


    /**
     * Store user custom file extension 
     *
     * @var string
     */
    private $_customFileExtension = '';



    /**
     * Execute user request for create  custom file
     *
     * @param Request $request
     * @param LogController $logController
     * @return Response
     */
    public function create(CreateFileRequest $request, LogController $logController) {

        $this->_username = auth()->user()->name;
        $this->_customFileTitle = $request->title;
        $this->_customFileExtension = $request->extension;

        $log = [
            'request' => json_encode($request->all()),
        ];

        try {

            $this->_createUserDirectory();
            $this->_createCustomFile();

            $successResponse = [
                'status' => true,
                'message' => 'Your file create successfully.',
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
     * Check user directory Exist and 
     * create username directory
     *
     * @return bool|Exception
     * @throws ApiOperationsException
     */
    private function _createUserDirectory()
    {                
        if(!file_exists(self::PARENT_DIRECTORY . '/' . $this->_username)) {

            $result = mkdir(self::PARENT_DIRECTORY . '/' . $this->_username, 0775, true);

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
     * Check custom file exist 
     * and create custom file
     *
     * @return bool|Exception
     * @throws ApiOperationsException
     */
    private function _createCustomFile()
    {    
        $secureFileTitle =  escapeshellcmd($this->_customFileTitle);
        $secureFileExtension =  escapeshellcmd($this->_customFileExtension);

        if(
            file_exists(
                self::PARENT_DIRECTORY . '/' . $this->_username . '/' . $secureFileTitle . '.' . $secureFileExtension
            )) 
        {

            throw new ApiOperationsException(
                'This imported file has already been created.',
                400
            );

        }else {
            
            $changeDirectoryCommand = 'cd ' . self::PARENT_DIRECTORY . '/' . $this->_username;
            $createFileCommand = ' && touch ' . $secureFileTitle . '.' . $secureFileExtension;

            exec($changeDirectoryCommand . $createFileCommand, $output, $resultcode);

            if($resultcode !== 0) {

                throw new ApiOperationsException(
                    'The operation is not execute and your file not create !',
                    500
                );
            }

            return true;
        }
    }
}
