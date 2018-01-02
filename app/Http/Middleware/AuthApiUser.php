<?php

namespace App\Http\Middleware;

use App\Http\Entity\Auth;
use Closure;

class AuthApiUser
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
        if (Auth::apiUserCheck($request)) {
            return $next($request);
        }
        return response()->json(['code' => 401, 'msg' => 'Unauthorized '], 200);
    }
}
