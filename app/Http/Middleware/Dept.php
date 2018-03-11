<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Dept
{
    use AuthorizesRequests;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() &&  Auth::user()->hasRole('dept')) {
            return $next($request);
        }

        abort(404, 'Yor are not authorized to page access'); 
    }
}
