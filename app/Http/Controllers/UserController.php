<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
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
        /**
         * Add the primary role default for now
         */
        $request['primary_role'] = Role::where('name', 'default')->first()->role_id;

        /** @var User $user */
        $user = parent::post($request);

        event(new UserCreated($user));

        return $this->response->item($user, $this->getTransformer())->setStatusCode(201);
    }

    /**
     * Check if email exists
     * @param $email
     * @return mixed
     */
    public function checkIfEmailExists($email)
    {
        $exists = User::where('email', $email)->exists();
        if ($exists) {
            return $this->response->array(['exists' => $exists]);
        }
        throw new NotFoundHttpException('User does not exist');
    }


    /**
     * Handle a registration request for the application.
     *
     * @param $token
     * @return \Illuminate\Http\Response
     */
    public function verify($token)
    {
        $user = User::where('email_token', $token)->first();
        if (! $user ) {
            throw new NotFoundHttpException('No user with this token found');
        }

        $user->verified = true;
        $user->save();

        return $this->response->noContent();
    }
    /**
     * Overriding User's patch so to pass in the userId on validation
     *
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

        //Passing the userId into the validation rules to not have a duplicate error when the user is updated
        $validator = Validator::make($data, array_intersect_key($user->getValidationRules($user->user_id), $data), $user->getValidationMessages());

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not update User with UUID "'.$user->getKey().'".', $validator->errors());
        }

        $user->update($data);

        return $this->response->item($user, $this->getTransformer());
    }

    /**
     * Return the user object for the logged in user
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function me(Request $request)
    {
        return $this->response->item($this->auth->user(), new BaseTransformer);
    }

    /**
     * Store an uploaded photo
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function storePhoto(Request $request)
    {
        if ($request->hasFile('photo')) {

            $user = $this->auth->user();

            $photo = $request->photo;

            $validator = Validator::make(['photo' => $photo], $user->getValidationPhotoRules());

            if ($validator->fails()) {
                throw new StoreResourceFailedException('Could not save the photo, please try again', $validator->errors());
            }

            $path = $photo->store('storage/uploads', 'public');

            $user->image = $path;
            $user->save();

            return $this->response->item($user, new BaseTransformer);
        }
        throw new BadRequestHttpException();
    }

    /**
     * Output the photo
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getPhoto(User $user)
    {

        if (Storage::exists('public/'.$user->image)) {
            return Storage::download('public/'.$user->image);
        }
        throw new NotFoundHttpException();
    }
}
