# Laravel Advanced SEO

A modular Laravel SEO package for meta tags, Open Graph, schema, sitemaps, redirects, 404 logging, SEO text files, image/link SEO helpers, breadcrumbs, HTML minification, and PageForge bulk SEO landing page generation.

## Features

- Dynamic meta tags, canonical URLs, Open Graph, and Twitter card tags.
- WebSite, Organization, WebPage, model, and LocalBusiness JSON-LD schema.
- XML sitemap at `/sitemap.xml` and Blade-rendered HTML sitemap support.
- Static SEO files for `/robots.txt`, `/llms.txt`, and `/.well-known/security.txt`.
- Redirect manager support for 301, 302, 410, and 451 responses.
- 404 monitor storage for broken URL tracking.
- Automatic image SEO attributes and external link controls.
- Breadcrumb service and Blade component.
- Optional HTML minification middleware.
- PageForge admin generator for bulk SEO landing pages.
- Protected package admin routes with configurable middleware.
- Optional admin sidebar link installation during `seo:install`.

## Requirements

- PHP 8.0 or newer
- Laravel 9 or newer
- Composer
- Database connection configured in `.env`

## Installation

### Install From Packagist

If the package is published on Packagist, install it normally:

```bash
composer require nirjon/laravel-seo
```

### Install From GitHub Branch

Production package work is maintained on the `package-release` branch:

```text
https://github.com/nirjonroy/seo-entiantials/tree/package-release
```

If Composer can read the GitHub branch as a VCS repository, add this to the host application's root `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/nirjonroy/seo-entiantials.git"
        }
    ],
    "require": {
        "nirjon/laravel-seo": "dev-package-release"
    }
}
```

Then install or update the dependency:

```bash
composer clear-cache
composer require nirjon/laravel-seo:dev-package-release -W
php artisan seo:install
php artisan vendor:publish --tag=seo-views --force
php artisan optimize:clear
```

The GitHub branch URL `https://github.com/nirjonroy/seo-entiantials/tree/package-release` is usable as a source branch because the branch contains the package `composer.json` at the repository root. In Composer, require the branch as `dev-package-release`; do not put `/tree/package-release` in the repository URL.

### Guaranteed GitHub Branch Install

If Composer says `nirjon/laravel-seo could not be found in any version`, use the explicit package repository method below. This is useful on cPanel/shared hosting when the package is not available on Packagist.

Add this full `repositories` block to the host application's root `composer.json`:

```json
"repositories": [
    {
        "type": "package",
        "package": {
            "name": "nirjon/laravel-seo",
            "version": "dev-package-release",
            "source": {
                "url": "https://github.com/nirjonroy/seo-entiantials.git",
                "type": "git",
                "reference": "61ccc89"
            },
            "require": {
                "php": "^8.0",
                "illuminate/support": "^9.0|^10.0|^11.0"
            },
            "autoload": {
                "psr-4": {
                    "Nirjon\\LaravelSeo\\": "src/"
                }
            },
            "extra": {
                "laravel": {
                    "providers": [
                        "Nirjon\\LaravelSeo\\SEOServiceProvider"
                    ]
                }
            }
        }
    }
]
```

Then add the package requirement:

```json
"require": {
    "nirjon/laravel-seo": "dev-package-release"
}
```

For a first install, run:

```bash
composer clear-cache
composer update nirjon/laravel-seo -W
php artisan seo:install
php artisan vendor:publish --tag=seo-views --force
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
```

When the `package-release` branch receives a new package-code fix, update `reference` to the latest commit hash from GitHub, then run the update commands below. The example above currently uses `61ccc89`, which includes the meta tag image upload update.

```bash
composer clear-cache
composer update nirjon/laravel-seo -W
php artisan vendor:publish --tag=seo-views --force
php artisan migrate --force
php artisan optimize:clear
```

For production, the better solution is to create a tagged release such as `v1.0.0` and require it with a stable constraint:

```json
{
    "require": {
        "nirjon/laravel-seo": "^1.0"
    }
}
```

Run the package installer:

```bash
php artisan seo:install
php artisan optimize:clear
```

The installer publishes `config/seo.php`, runs package migrations, and tries to add the SEO Settings link to `resources/views/admin/sidebar.blade.php` when that file exists and is writable.

After changing package classes or Composer path repositories, refresh autoloading:

```bash
composer dump-autoload
```

## Main URLs

| URL | Purpose |
| --- | --- |
| `/sitemap.xml` | XML sitemap |
| `/robots.txt` | Robots file |
| `/llms.txt` | LLM crawler information file |
| `/.well-known/security.txt` | Security contact file |
| `/admin/seo-admin/settings` | SEO module settings UI |
| `/admin/seo-admin/generator` | PageForge bulk page generator |
| `/{slug}` | Public generated SEO landing page |

## Configuration

The package config is published to:

```text
config/seo.php
```

Important sections:

- `modules`: enable or disable package features.
- `admin.middleware`: middleware for package admin routes.
- `admin.sidebar`: sidebar auto-install settings.
- `layout` and `section`: Blade layout and section for package admin pages.
- `defaults`: site name, title separator, author, publisher, copyright, and default image.
- `sitemap`: sitemap settings and model classes.
- `files`: robots, llms, and security text content.
- `fallbacks`: fallback title and description templates.
- `verifications`: search engine verification codes.
- `organization`: Organization and WebSite schema values.
- `scripts`: custom head/body/footer scripts.
- `breadcrumbs`: breadcrumb output settings.
- `links`: external link behavior.

## Admin Route Security

Package admin URLs are protected by `config('seo.admin.middleware')` and a built-in package guard that blocks guests even if a host project accidentally removes `auth` from the published config.

Default:

```php
'admin' => [
    'middleware' => ['web', 'auth'],
],
```

For an application with a dedicated admin guard:

```php
'admin' => [
    'middleware' => ['web', 'auth:admin'],
],
```

For an application that accepts both admin and web sessions:

```php
'admin' => [
    'middleware' => ['web', 'auth:admin,web'],
],
```

This protects `/admin/seo-admin/settings`, `/admin/seo-admin/generator`, and related admin API routes from guest access. Public generated pages at `/{slug}` remain visible.

If a project has no named `login` route, unauthenticated users receive `403 Forbidden`. If a `login` route exists, guests are redirected there.

## Admin Sidebar Link

During `php artisan seo:install`, the package tries to add this include to the host admin sidebar:

```blade
@include('seo::admin.sidebar-link')
```

Default target:

```text
resources/views/admin/sidebar.blade.php
```

To change or disable this behavior:

```php
'admin' => [
    'sidebar' => [
        'auto_install' => true,
        'path' => resource_path('views/admin/sidebar.blade.php'),
    ],
],
```

If the installer cannot find a writable sidebar file, add the include manually where the `SEO Settings` button should appear.

## SEO Tags

Place the component inside the document `<head>`:

```blade
<x-seo::tags />
```

Admins can manage URL-level meta tags from:

```text
/admin/seo-admin/meta-tags
```

The Meta Tags screen stores metadata against public URL paths such as `/about-us`, `/blog`, or `/services/computer`. When `<x-seo::tags />` is present in the host layout, the component automatically checks the current request path and uses the saved title, description, keywords, canonical URL, robots tag, Open Graph fields, Twitter fields, author, publisher, copyright, and site name. Explicit values passed to the component still take priority.

Manual values:

```blade
<x-seo::tags
    title="About Us"
    description="Learn more about our company."
    keywords="company, services"
    canonical="{{ url('/about-us') }}"
    ogTitle="About Our Company"
    ogDescription="Learn more about our company."
    image="{{ asset('images/og-image.jpg') }}"
    author="Nirjon Roy"
    publisher="Example Publisher"
    copyright="2026 Example"
    robots="index, follow"
/>
```

You can also pass a model that uses `Nirjon\LaravelSeo\Traits\HasSeo`:

```blade
<x-seo::tags :model="$product" />
```

The component outputs title, description, keywords, robots, canonical, author, publisher, copyright, Open Graph, Twitter, hreflang, verification tags, custom scripts, and JSON-LD schema.

## Model SEO

Add the trait to any Eloquent model:

```php
use Nirjon\LaravelSeo\Traits\HasSeo;

class Product extends Model
{
    use HasSeo;
}
```

Save SEO data:

```php
$product->updateSeo([
    'meta_title' => 'Best Laptop Deals',
    'meta_description' => 'Find current laptop offers.',
    'meta_keywords' => 'laptop, deals, electronics',
    'canonical_url' => url('/products/best-laptop-deals'),
    'schema_type' => 'Product',
]);
```

## Sitemaps

Add models to `config/seo.php`:

```php
'sitemap' => [
    'models' => [
        \App\Models\Product::class,
    ],
],
```

Each model should expose a URL:

```php
public function getSitemapUrl()
{
    return url('/products/' . $this->slug);
}
```

Render an HTML sitemap in Blade:

```blade
@htmlSitemap
```

PageForge generated pages are included in the sitemap.

The package also supports a database-configurable public sitemap filename from:

```text
/admin/seo-admin/settings
```

In the Sitemap settings panel, set a name such as:

```text
seo-entiantials.xml
```

Then the dynamic XML sitemap is available at:

```text
/seo-entiantials.xml
```

The default `/sitemap.xml` endpoint remains available for compatibility. The settings panel shows the current sitemap URL and the PageForge generated pages included in the XML output.

Dynamic sitemap options include:

- custom sitemap index filename, for example `seo-essentials.xml` or `repair-services-sitemap.xml`
- URL count per child sitemap file
- child sitemap filename pattern using `{base}` and `{page}`, for example `{base}-{page}.xml`
- PageForge generated page preview count inside the settings screen

When generated page URLs exceed the configured limit, the package creates a sitemap index and numbered child sitemap URLs automatically:

```text
/repair-services-sitemap.xml
/repair-services-sitemap-1.xml
/repair-services-sitemap-2.xml
```

PageForge generated pages are included in these XML files with priority `1.0`.

If a host project has middleware that calls `$response->header()`, update to the latest package release. Sitemap responses are returned as normal Laravel responses, including child sitemap files, so they do not break CORS or custom response middleware.

## PageForge Bulk Page Generator

Open:

```text
/admin/seo-admin/generator
```

PageForge creates landing pages from:

- title template
- slug template
- TinyMCE content editor
- meta title, description, and keywords
- optional featured image for Open Graph and visible page hero media
- optional page logo
- generated-page design controls for colors, font family, max content width, and custom CSS
- author, publisher, copyright, and site name fields
- two keyword bundles

Generated pages are stored in `nirjon_seo_generated_pages` and shown at `/{slug}`.

The PageForge admin UI includes:

- a `Use Demo Data` modal for quickly filling a working template
- a `CSS Classes` modal with available generated-page classes and sample CSS
- an `Edit` button in the generated pages table that loads the page template back into the form
- `View` and `Delete` actions for generated pages

The public generated page view normalizes stored HTML, resolves leftover spintax for legacy rows, emits one `<title>` tag, and passes robots, author, publisher, copyright, image, canonical, and Open Graph values to the SEO component. When a featured image is uploaded, it is displayed visually below the hero title and reused in related-page cards. The package also exposes `/seo-media/{path}` for PageForge uploads, so generated page logos and featured images still render even before a host app creates a public storage symlink. The view also shows related generated pages below the main content, preferring pages from the same template and falling back to recent generated pages.

Admins can customize generated page presentation from the PageForge form:

- upload a logo for the generated page header
- choose primary, accent, background, and text colors
- set the font family and page container width
- add custom CSS for detailed control over generated page sections such as `.pf-page`, `.pf-header`, `.pf-card`, `.pf-hero`, `.pf-title`, and `.pf-content`

The content editor uses TinyMCE with formatting, alignment, lists, links, images, media, tables, blockquotes, code samples, source code, fullscreen, and preview tools.

## PageForge Placeholders And Spintax

Two keyword bundle placeholders are supported:

```text
{0}
{1}
```

Example:

```text
Title: {Best|Top|Reliable} {0} services in {1}
Slug: {0}-services-{1}
Content: <p>We provide {fast|trusted|professional} {0} services in {1}.</p>
```

With:

```text
Bundle 1: Web Development, SEO Audit
Bundle 2: Dhaka, Sylhet
```

The generator creates every keyword combination and randomly chooses one spintax option for each generated page.

## Uploaded Images

Featured images are stored on the public disk under:

```text
storage/app/public/seo-images
```

Create the public storage link:

```bash
php artisan storage:link
```

## Publishing Views

To override package views:

```bash
php artisan vendor:publish --tag=seo-views
```

Published views are copied to:

```text
resources/views/vendor/seo
```

Laravel loads published views before package views. If you publish views, keep them updated when upgrading the package.

## Useful Commands

```bash
php artisan seo:install
php artisan migrate
php artisan storage:link
php artisan optimize:clear
composer dump-autoload
```

## Verify Installed Package Version

After updating on a server, check that the installed vendor copy contains the latest sitemap compatibility code:

```bash
grep -n "function canServe" vendor/nirjon/laravel-seo/src/Services/SitemapService.php
grep -n "response()->stream" vendor/nirjon/laravel-seo/src/Http/Middleware/GeneratedPageFallbackMiddleware.php
grep -n "generateXml" vendor/nirjon/laravel-seo/src/Http/Middleware/GeneratedPageFallbackMiddleware.php
```

Expected result:

- `function canServe` is found in `SitemapService.php`
- `response()->stream` returns no lines
- `generateXml` is found in `GeneratedPageFallbackMiddleware.php`

Then clear caches:

```bash
php artisan optimize:clear
```

## Troubleshooting

- If admin URLs are visible to guests, check `seo.admin.middleware` and clear caches with `php artisan optimize:clear`.
- If logged-in admins are redirected to login, use the correct guard, for example `auth:admin` or `auth:admin,web`.
- If the sidebar link does not appear, add `@include('seo::admin.sidebar-link')` manually to the host sidebar.
- If generated pages return 404, confirm the package catch-all `/{slug}` route is loaded after specific frontend routes.
- If uploaded images are not public, run `php artisan storage:link`.
- If published views show old behavior, update files in `resources/views/vendor/seo`.

## License

The MIT License (MIT). See `LICENSE.md` for details.
