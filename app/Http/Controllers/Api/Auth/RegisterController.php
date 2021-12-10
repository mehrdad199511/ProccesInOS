<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\ApiAuthException;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Http\Controllers\Api\Log\LogController;

/**
 * Class RegisterController
 * @tag Auth
 * @namespace App\Http\Controllers\Api\Auth
 */
class RegisterController extends Controller
{
    /**
     * Store register request data
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
     * Store created user
     *
     * @var object
     */
    private $_user = null;

    /**
     * Do user register
     *
     * @param RegisterRequest $request
     * @param UserRepository $userRepo
     * @param LogController $logController
     * @return Response
     */
    public function register(
        RegisterRequest $request,
        UserRepository $userRepo,
        LogController $logController
    ) {
        $this->_requestData = $request->all();

        // Hashing user password
        $this->_requestData['password'] = Hash::make($this->_requestData['password']);

        $log = [
            'request' => json_encode($this->_requestData),
        ];

        try {

            $this->_createUser($userRepo);
            $this->_setAccessToken();

            $successResponse = [
                'message' => 'User creation is successful!',
                'user' => new UserResource($this->_user),
                'token' => $this->_accessToken
            ];

            $log['response'] = json_encode($successResponse);
            $log['code'] = 200;

            return response($successResponse, 201);

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
     * Create new user
     *
     * @param UserRepository $userRepo
     * @return bool
     * @throws ApiAuthException
     */
    private function _createUser(UserRepository $userRepo)
    {
        $this->_user = $userRepo->create($this->_requestData);

        if (is_object($this->_user)) {
            return true;
        }

        throw new ApiAuthException("User creation failure!", 500);
    }


    /**
     * Set access token for registered user
     */
    private function _setAccessToken()
    {
        $this->_accessToken = $this->_user->createToken('UserToken')->plainTextToken;;
    }
}
