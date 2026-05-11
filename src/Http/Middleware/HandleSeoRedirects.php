<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleSeoRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        $redirect = \Nirjon\LaravelSeo\Models\SeoRedirect::where('is_active', 1)
            ->where(function($query) use ($path) {
                $query->where('source_url', $path)
                      ->orWhere('source_url', '/' . $path);
            })
            ->first();

        if ($redirect) {
            return redirect($redirect->destination_url, $redirect->redirect_type);
        }

        return $next($request);
    }
}
