<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class variablePickup
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
	    // Store changes to selected year
	    if(Input::has('year')) {
		    $year = Input::get('year');
		    if($year == '') {
			    Session::forget('year');
		    } else {
			    Session::put('year', $year);
		    }
	    }

	    // Store changes to selected level
	    if(Input::has('level_select')) {
		    $level_select = Input::get('level_select');
		    if($level_select == 0) {
			    Session::forget('level_select');
		    } else {
			    Session::put('level_select', $level_select);
		    }
	    }

        return $next($request);
    }
}
