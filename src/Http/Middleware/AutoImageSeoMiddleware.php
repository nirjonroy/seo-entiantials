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
                    $attributesToAdd = '';

                    $hasAlt = stripos($imgTag, 'alt=') !== false;
                    $hasTitle = stripos($imgTag, 'title=') !== false;
                    $hasLoading = stripos($imgTag, 'loading=') !== false;

                    if (!$hasAlt || !$hasTitle) {
                        if (preg_match('/src=["\']([^"\']+)["\']/i', $imgTag, $srcMatches)) {
                            $src = $srcMatches[1];
                            $filename = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_FILENAME);
                            $cleanedFilename = htmlspecialchars(ucwords(str_replace(['-', '_'], ' ', $filename)), ENT_QUOTES);

                            if (!$hasAlt) {
                                $attributesToAdd .= ' alt="' . $cleanedFilename . '"';
                            }
                            if (!$hasTitle) {
                                $attributesToAdd .= ' title="' . $cleanedFilename . '"';
                            }
                        }
                    }

                    if (!$hasLoading) {
                        $attributesToAdd .= ' loading="lazy"';
                    }

                    if (!empty($attributesToAdd)) {
                        return preg_replace('/<img/i', '<img' . $attributesToAdd, $imgTag, 1);
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
