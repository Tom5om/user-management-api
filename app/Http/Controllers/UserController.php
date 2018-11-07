<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Transformers\BaseTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;

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

    /**
     * Request to update the specified resource
     *
     * @param string $uuid UUID of the resource
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws HttpException
     */
    public function patch($uuid, Request $request)
    {
        /** @var User $user */
        $user = User::findOrFail($uuid);

        $this->authorizeUserAction('update', $user);

        $data = $request->input();

        $validator = Validator::make($data, array_intersect_key($user->getValidationRules($user->user_id), $data), $user->getValidationMessages());

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not update resource with UUID "'.$user->getKey().'".', $validator->errors());
        }

        $user->update($data);

        if ($this->shouldTransform()) {
            $response = $this->response->item($user, $this->getTransformer());
        } else {
            $response = $user;
        }

        return $response;
    }

    public function me(Request $request)
    {
        return $this->response->item($this->auth->user(), new BaseTransformer);
    }

    public function storePhoto(Request $request)
    {
        if ($request->hasFile('photo')) {
            $path = $request->photo->store('storage/uploads', 'public');

            $user = $this->auth->user();
            $user->image = $path;
            $user->save();

            return $this->response->item($user, new BaseTransformer);
        }
        throw new BadRequestHttpException();
    }

    public function getPhoto(Request $request)
    {
        $user = User::findOrFail($request->id);

        if (Storage::exists('public/'.$user->image)) {
            return Storage::download('public/'.$user->image);
        }
        throw new NotFoundHttpException();
    }
}
