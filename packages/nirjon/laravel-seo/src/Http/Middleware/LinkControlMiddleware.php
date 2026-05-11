<?php

namespace Nirjon\LaravelSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkControlMiddleware
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

        if (config('seo.links.enabled') && $response->isSuccessful() && str_contains($contentType, 'text/html')) {
            $html = $response->getContent();

            if (is_string($html)) {
                $host = $request->getHost();

                $modifiedHtml = preg_replace_callback('/<a\s+([^>]+)>/i', function ($matches) use ($host) {
                    $attributesStr = $matches[1];
                    $aTag = $matches[0];

                    if (preg_match('/href=["\']([^"\']+)["\']/i', $attributesStr, $hrefMatches)) {
                        $href = $hrefMatches[1];
                        $linkHost = parse_url($href, PHP_URL_HOST);

                        // If there is a host and it's different from the request host, it's an external link
                        if ($linkHost && $linkHost !== $host) {
                            $modifiedAttributesStr = $attributesStr;

                            if (config('seo.links.external_new_tab')) {
                                if (stripos($modifiedAttributesStr, 'target=') === false) {
                                    $modifiedAttributesStr .= ' target="_blank"';
                                }
                            }

                            if (config('seo.links.external_nofollow')) {
                                if (preg_match('/rel=["\']([^"\']+)["\']/i', $modifiedAttributesStr, $relMatches)) {
                                    $currentRel = $relMatches[1];
                                    $newRels = ['nofollow', 'noopener', 'noreferrer'];
                                    
                                    // Split current rel by spaces, filter out empty strings
                                    $rels = array_filter(explode(' ', $currentRel));
                                    
                                    $changed = false;
                                    foreach ($newRels as $rel) {
                                        if (!in_array($rel, $rels, true)) {
                                            $rels[] = $rel;
                                            $changed = true;
                                        }
                                    }
                                    
                                    if ($changed) {
                                        $newRelStr = implode(' ', $rels);
                                        $modifiedAttributesStr = preg_replace('/rel=["\'][^"\']+["\']/i', 'rel="' . $newRelStr . '"', $modifiedAttributesStr);
                                    }
                                } else {
                                    $modifiedAttributesStr .= ' rel="nofollow noopener noreferrer"';
                                }
                            }

                            return '<a ' . trim($modifiedAttributesStr) . '>';
                        }
                    }

                    return $aTag;
                }, $html);

                if ($modifiedHtml !== null) {
                    $response->setContent($modifiedHtml);
                }
            }
        }

        return $response;
    }
}
