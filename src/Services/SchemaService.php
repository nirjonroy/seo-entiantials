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

        if ($localBusiness = $this->generateLocalBusinessSchema()) {
            $schemas[] = $localBusiness;
        }

        $json = json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return '<script type="application/ld+json">' . $json . '</script>';
    }

    /**
     * Generate LocalBusiness schema based on config.
     *
     * @return array|null
     */
    public function generateLocalBusinessSchema(): ?array
    {
        if (!config('seo.modules.local_seo')) {
            return null;
        }

        $localBusiness = config('seo.local_business');

        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'LocalBusiness',
            'name'       => $localBusiness['name'] ?? null,
            'image'      => $localBusiness['image'] ?? null,
            'telephone'  => $localBusiness['telephone'] ?? null,
            'priceRange' => $localBusiness['priceRange'] ?? null,
            'address'    => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $localBusiness['address']['streetAddress'] ?? null,
                'addressLocality' => $localBusiness['address']['addressLocality'] ?? null,
                'postalCode'      => $localBusiness['address']['postalCode'] ?? null,
                'addressCountry'  => $localBusiness['address']['addressCountry'] ?? null,
            ],
            'geo'        => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => $localBusiness['geo']['latitude'] ?? null,
                'longitude' => $localBusiness['geo']['longitude'] ?? null,
            ],
        ];
    }
}
