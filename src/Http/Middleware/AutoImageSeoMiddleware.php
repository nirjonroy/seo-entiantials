<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoImageSeoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $contentType = $response->headers->get('Content-Type', '');

        if (config('seo.modules.image_seo') && $response->isSuccessful() && str_contains($contentType, 'text/html')) {
            $html = $response->getContent();

            if (is_string($html)) {
                $modifiedHtml = preg_replace_callback('/<img\s+[^>]*>/i', function ($matches) {
                    $imgTag = $matches[0];

                    if (stripos($imgTag, 'alt=') === false) {
                        if (preg_match('/src=["\']([^"\']+)["\']/i', $imgTag, $srcMatches)) {
                            $src = $srcMatches[1];
                            $filename = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_FILENAME);
                            $cleanedFilename = ucwords(str_replace(['-', '_'], ' ', $filename));

                            return preg_replace('/<img/i', '<img alt="' . htmlspecialchars($cleanedFilename, ENT_QUOTES) . '"', $imgTag, 1);
                        }
                    }

                    return $imgTag;
                }, $html);

                if ($modifiedHtml !== null) {
                    $response->setContent($modifiedHtml);
                }
            }
        }

        return $response;
    }
}
