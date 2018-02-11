<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Helpers\Roles;

class teacherFilter
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
        if (Auth::guest()) {
            return redirect()->guest('login');
        } else {
            if(!Roles::isTeacher()) {
                return "You are not a Teacher";
            }
        }
        return $next($request);
    }
}
