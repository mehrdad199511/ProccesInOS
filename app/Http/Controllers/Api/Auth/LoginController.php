<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Log\LogController;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Exceptions\ApiAuthException;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

/**
 * Class LoginController
 * @tag Auth
 * @namespace App\Http\Controllers\Api\Auth
 */
class LoginController extends Controller
{
    /**
     * Store login request data
     *
     * @var array
     */
    private $_requestData = [];

    /**
     * Store user access token
     *
     * @var string
     */
    private $_accessToken = '';

    /**
     * Store authenticated user
     *
     * @var object
     */
    private $_user = null;

    /**
     * Do login
     *
     * @param LoginRequest $request
     * @param LogController $logController
     * @return Response
     */
    public function login(LoginRequest $request, LogController $logController)
    {
        $this->_requestData = $request->all();
    
        $log = [
            'request' => json_encode($this->_requestData),
        ];

        try {

            $this->_checkAuthAttempt();
            $this->_getUser();
            $this->_setAccessToken();
         
            $successResponse = [
                'message' => 'Successful login!',
                'user' => new UserResource($this->_user),
                'token' => $this->_accessToken
            ];
            
            $log['response'] = json_encode($successResponse);
            $log['code'] = 200;
            
            return response()->json($successResponse, 200);
            
        } catch (ApiAuthException $e) {
            $e->customReport($e);
            
            $log['response'] = $e->getMessage();
            $log['code'] = $e->getCode();
            
            return response(['message' => $e->getMessage()], $e->getCode());

        } finally {
    
            $logController->logger($request, $log);
        }
    }

    /**
     * Checking user authentication
     *
     * @return bool
     * @throws ApiAuthException
     */
    private function _checkAuthAttempt()
    {
        if (!auth()->attempt($this->_requestData)) {
            throw new ApiAuthException('Email or Password is wrong!', 422);
        }

        return true;
    }

    /**
     * Get authenticated user
     *
     * @return bool
     * @throws ApiAuthException
     */
    private function _getUser()
    {
        $this->_user = auth()->user();

        if (is_null($this->_user)) {
            throw new ApiAuthException('User not found!', 404);
        }

        return true;
    }

    /**
     * Create access token for authenticated user
     */
    private function _setAccessToken()
    {
        $this->_accessToken = $this->_user->createToken('UserToken')->plainTextToken;
    }
}
