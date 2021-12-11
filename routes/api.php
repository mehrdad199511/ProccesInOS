<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|



/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::namespace('App\Http\Controllers\Api\Auth')
    ->prefix('auth')
    ->group(
        function() {

            Route::post('/register', 'RegisterController@register');
            Route::post('/login', 'LoginController@login');
        }
    );
    


/*
|--------------------------------------------------------------------------
| Processes Routes
|--------------------------------------------------------------------------
*/

Route::namespace('App\Http\Controllers\Api\Operations')
    ->prefix('operation')
    ->middleware('auth:sanctum')
    ->group(
        function() {

            Route::get('/getProcesses', 'GetProcessesController@getProcesses');
            Route::post('/createDirectory', 'CreateDirectoryController@create');
        }
    );
    