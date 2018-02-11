<?php

namespace App\Http\Middleware;

use Artisan;
use Closure;

class ClearViewCache
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
        if (env('APP_ENV') === 'local') {
            Artisan::call('view:clear');
        }

        return $next($request);
    }
}
