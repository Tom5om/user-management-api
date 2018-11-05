<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static $model = User::class;

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function post(Request $request)
    {
        $request['primary_role'] = Role::where('name', 'default')->first()->role_id;
        $response = parent::post($request);
        return $response;
    }
}
