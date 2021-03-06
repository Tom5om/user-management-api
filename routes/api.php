<?php

use App\Http\Middleware\CheckIsVerified;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Welcome route - link to any public API documentation here
 */
Route::get('/', function () {
    echo 'Welcome to our API';
});


/**
 * @var $api \Dingo\Api\Routing\Router
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => ['api']], function ($api) {
    /**
     * Authentication
     */
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->group(['prefix' => 'jwt'], function ($api) {
            $api->group(['middleware' => ['is_email_verified']], function ($api) {
                $api->get('/token', 'App\Http\Controllers\Auth\AuthController@token');
            });
        });
    });

    $api->group(['prefix' => 'users'], function ($api) {
        $api->post('/', 'App\Http\Controllers\UserController@post');
        $api->get('/{user}/photo/{path?}', 'App\Http\Controllers\UserController@getPhoto')->where('path', '(.*)');
        $api->get('/verification/{token}', 'App\Http\Controllers\UserController@verify');
        $api->get('/email/{email}', 'App\Http\Controllers\UserController@checkIfEmailExists');
    });

    /**
     * Authenticated routes
     */
    $api->group(['middleware' => ['api.auth']], function ($api) {
        /**
         * Authentication
         */
        $api->group(['prefix' => 'auth'], function ($api) {
            $api->group(['prefix' => 'jwt'], function ($api) {
                $api->get('/refresh', 'App\Http\Controllers\Auth\AuthController@refresh');
                $api->delete('/token', 'App\Http\Controllers\Auth\AuthController@logout');
            });

//            $api->get('/me', 'App\Http\Controllers\Auth\AuthController@getUser');
            $api->get('/me', 'App\Http\Controllers\UserController@me');
        });

        /**
         * Users
         */
        $api->group(['prefix' => 'users', 'middleware' => 'check_role:default'], function ($api) {
            $api->get('/', 'App\Http\Controllers\UserController@getAll');
            $api->get('/{uuid}', 'App\Http\Controllers\UserController@get');
            $api->put('/{uuid}', 'App\Http\Controllers\UserController@put');
            $api->patch('/{uuid}', 'App\Http\Controllers\UserController@patch');
            $api->delete('/{uuid}', 'App\Http\Controllers\UserController@delete');
            $api->post('/me/photo', 'App\Http\Controllers\UserController@storePhoto');
        });


        /**
         * Roles
         */
        $api->group(['prefix' => 'roles'], function ($api) {
            $api->get('/', 'App\Http\Controllers\RoleController@getAll');
        });
    });
});
