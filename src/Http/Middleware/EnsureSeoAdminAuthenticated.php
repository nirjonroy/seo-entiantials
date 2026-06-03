<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class EnsureSeoAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        foreach (array_keys((array) config('auth.guards', [])) as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        if (Route::has('login')) {
            return redirect()->guest(route('login'));
        }

        abort(403);
    }
}
