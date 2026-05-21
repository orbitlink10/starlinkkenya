<?php

namespace App\Support;

use App\Models\Product;
use App\Models\SitePage;
use Illuminate\Support\Str;

class SeoData
{
    public static function siteName(): string
    {
        return (string) config('seo.site_name', 'Starlink Kenya');
    }

    public static function defaultDescription(): string
    {
        return (string) config('seo.default_description', 'Professional Starlink installation and genuine hardware in Kenya.');
    }

    public static function trimDescription(?string $value, int $limit = 170): string
    {
        $plainText = trim((string) preg_replace(
            '/\s+/u',
            ' ',
            strip_tags(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
        ));

        if ($plainText === '') {
            $plainText = static::defaultDescription();
        }

        return Str::limit($plainText, $limit, '');
    }

    public static function mediaUrl(?string $path): ?string
    {
        $normalizedPath = trim((string) $path, '/');

        if ($normalizedPath === '') {
            return null;
        }

        return route('media.show', ['path' => $normalizedPath]);
    }

    public static function sanitizeCommercialLinks(string $html): string
    {
        $blockedHosts = collect(config('seo.strip_link_hosts', []))
            ->map(fn ($host): string => strtolower(ltrim((string) preg_replace('/^www\./i', '', trim((string) $host)), '.')))
            ->filter()
            ->values()
            ->all();

        if ($html === '' || $blockedHosts === []) {
            return $html;
        }

        return preg_replace_callback(
            '/<a\b[^>]*href=(["\'])(.*?)\1[^>]*>(.*?)<\/a>/isu',
            static function (array $matches) use ($blockedHosts): string {
                $href = html_entity_decode(trim((string) ($matches[2] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $host = parse_url($href, PHP_URL_HOST);

                if (! is_string($host) || $host === '') {
                    return $matches[0];
                }

                $normalizedHost = strtolower((string) preg_replace('/^www\./i', '', $host));

                foreach ($blockedHosts as $blockedHost) {
                    if ($normalizedHost === $blockedHost || str_ends_with($normalizedHost, '.'.$blockedHost)) {
                        return $matches[3];
                    }
                }

                return $matches[0];
            },
            $html
        ) ?? $html;
    }

    /**
     * @param  array<int, array{name:string, url:string}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)
                ->values()
                ->map(fn (array $item, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ])
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function websiteSchema(?string $imageUrl = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => static::siteName(),
            'url' => route('home'),
            'description' => static::defaultDescription(),
            'inLanguage' => 'en-KE',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('home').'?q={search_term_string}#packages',
                'query-input' => 'required name=search_term_string',
            ],
        ];

        if ($imageUrl !== null) {
            $schema['image'] = $imageUrl;
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>
     */
    public static function organizationSchema(?string $imageUrl = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => static::siteName(),
            'url' => route('home'),
            'telephone' => (string) config('seo.phone'),
            'areaServed' => [
                '@type' => 'Country',
                'name' => (string) config('seo.area_served', 'Kenya'),
            ],
        ];

        if ($imageUrl !== null) {
            $schema['logo'] = $imageUrl;
            $schema['image'] = $imageUrl;
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>
     */
    public static function localBusinessSchema(?string $imageUrl = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => static::siteName(),
            'url' => route('home'),
            'description' => static::defaultDescription(),
            'telephone' => (string) config('seo.phone'),
            'priceRange' => (string) config('seo.price_range'),
            'areaServed' => [
                '@type' => 'Country',
                'name' => (string) config('seo.area_served', 'Kenya'),
            ],
            'serviceType' => [
                'Starlink installation',
                'Starlink hardware supply',
                'Satellite internet setup',
            ],
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => (string) config('seo.phone'),
                    'contactType' => 'sales',
                    'areaServed' => 'KE',
                    'availableLanguage' => ['en', 'sw'],
                ],
            ],
            'knowsAbout' => [
                'Starlink Kenya',
                'Starlink installation in Kenya',
                'Starlink packages in Kenya',
                'Satellite internet Kenya',
            ],
        ];

        if ($imageUrl !== null) {
            $schema['image'] = $imageUrl;
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>
     */
    public static function productSchema(Product $product, string $url, string $description, ?string $imageUrl = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $description,
            'sku' => (string) $product->id,
            'url' => $url,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Starlink',
            ],
            'category' => $product->category?->name ?: 'Starlink hardware',
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'KES',
                'price' => number_format((float) $product->price, 2, '.', ''),
                'availability' => (int) ($product->quantity ?? $product->stock ?? 0) > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/PreOrder',
                'url' => $url,
            ],
        ];

        if ($imageUrl !== null) {
            $schema['image'] = [$imageUrl];
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>
     */
    public static function pageSchema(SitePage $page, string $url, string $description, ?string $imageUrl = null): array
    {
        $isArticle = strtolower((string) $page->type) === 'post';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $isArticle ? 'Article' : 'WebPage',
            'headline' => $page->page_title,
            'name' => $page->page_title,
            'description' => $description,
            'url' => $url,
            'inLanguage' => 'en-KE',
            'datePublished' => optional($page->created_at)->toAtomString(),
            'dateModified' => optional($page->updated_at ?: $page->created_at)->toAtomString(),
            'mainEntityOfPage' => $url,
        ];

        if ($imageUrl !== null) {
            $schema['image'] = [$imageUrl];
        }

        if ($isArticle) {
            $schema['author'] = [
                '@type' => 'Organization',
                'name' => static::siteName(),
            ];
            $schema['publisher'] = [
                '@type' => 'Organization',
                'name' => static::siteName(),
            ];
        }

        return array_filter($schema, fn ($value): bool => $value !== null);
    }
}
