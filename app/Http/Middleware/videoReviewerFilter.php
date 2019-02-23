<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Helpers\Roles;

class videoReviewerFilter
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
            if(!Roles::isVideoReviewer()) {
                return redirect()
	                ->to('\\')
                    ->with(['error' => "You do not have permission to review videos."]);
            }
        }
        return $next($request);
    }
}
