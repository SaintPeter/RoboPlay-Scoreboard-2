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
	            return redirect()
		            ->to('\\')
		            ->with(['error' => "You are not an Admin"]);
            }
        }
        return $next($request);
    }
}
