<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HtmlMinifierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // কনফিগ চেক করা
        if (config('seo.modules.minify_html', true)) {
            $html = $response->getContent();

            
            if (is_string($html) && str_contains(strtolower($html), '<html')) {
                $search = [
                    '/\>[^\S ]+/s',     
                    '/[^\S ]+\</s',     
                    '/(\s)+/s',         
                    '//' 
                ];
                $replace = ['>', '<', '\\1', ''];

                $minifiedHtml = preg_replace($search, $replace, $html);
                $response->setContent($minifiedHtml);
            }
        }

        return $response;
    }
}