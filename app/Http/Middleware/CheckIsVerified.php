<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Specialtactics\L5Api\Exceptions\UnauthorizedHttpException;

class CheckIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $authHeader = $request->header('Authorization');

        // Get for Auth Basic
        if ( strtolower(substr($authHeader, 0, 5)) !== 'basic') {
            throw new UnauthorizedHttpException('Invalid authorization header, should be type basic');
        }

        // Get credentials
        $credentials = base64_decode(trim(substr($authHeader, 5)));

        list ($email) = explode(':', $credentials);
        
        $user = User::where('email', $email)->first();

        if (! $user->verified) {
            abort(403, 'Your email address is not verified. Please check your email for the verification link');
        }
        
        return $next($request);
    }
}
