<?php

namespace Nirjon\LaravelSeo\Services;

class SchemaFactory
{
    public function generate($model)
    {
        if (!$model || !isset($model->seo) || empty($model->seo->schema_type)) {
            return null;
        }

        $type = $model->seo->schema_type;
        $data = $model->seo->schema_data ?? [];

        if (is_string($data)) {
            $data = json_decode($data, true) ?? [];
        }

        switch ($type) {
            case 'Article':
                return [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'Article',
                    'headline'    => $model->getSeoTitle(),
                    'description' => $model->getSeoDescription(),
                    'author'      => [
                        '@type' => 'Person',
                        'name'  => $data['author'] ?? 'Admin',
                    ],
                ];

            case 'Product':
                return [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'Product',
                    'name'        => $model->getSeoTitle(),
                    'description' => $model->getSeoDescription(),
                    'offers'      => [
                        '@type'         => 'Offer',
                        'price'         => $data['price'] ?? '0.00',
                        'priceCurrency' => 'USD',
                    ],
                ];

            default:
                return null;
        }
    }
}
