<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Helpers\Roles;

class judgeFilter
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
            if(!Roles::isJudge()) {
                return "You do not have permission to Judge";
            }
        }
        return $next($request);
    }
}
