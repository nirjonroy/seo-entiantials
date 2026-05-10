<?php

namespace Nirjon\LaravelSeo\Services;

class SchemaService
{
    /**
     * Generate global schemas for WebSite and Organization.
     *
     * @return string
     */
    public function generateGlobalSchemas(): string
    {
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => config('seo.defaults.site_name'),
            'url'      => url('/'),
        ];

        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => config('seo.defaults.site_name'),
            'url'      => url('/'),
            'logo'     => config('seo.defaults.default_image') ?: url('/logo.png'),
        ];

        $schemas = [
            $websiteSchema,
            $organizationSchema,
        ];

        $json = json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return '<script type="application/ld+json">' . $json . '</script>';
    }
}
