<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Helpers\Roles;

class adminFilter
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
            if(!Roles::isAdmin()) {
                return "You do not have permission to admin.";
            }
        }
        return $next($request);
    }
}
