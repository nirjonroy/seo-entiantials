# SEO Essentials - Laravel SEO User Manual

This project is a Laravel 9 application used to develop and test the local package `nirjon/laravel-seo`.

The package lives in:

```text
packages/nirjon/laravel-seo
```

It provides SEO meta tags, Open Graph/Twitter tags, schema output, XML and HTML sitemaps, SEO text files, redirects, 404 logging, image/link SEO middleware, breadcrumbs, HTML minification, and a bulk SEO page generator called PageForge.

## Requirements

- PHP 8.0 or newer
- Laravel 9 or newer
- Composer
- A configured database connection in `.env`
- Queue worker for background page generation

## Package Installation

The root project already installs the package from the local path repository:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/nirjon/laravel-seo"
    }
],
"require": {
    "nirjon/laravel-seo": "dev-main"
}
```

For a fresh install, users only need to run:

```bash
composer install
php artisan seo:install
```

The `seo:install` command is fully automated. It publishes the package configuration and runs the package database migrations on its own, including the SEO metadata, redirects, 404 logs, generated pages, and GUI settings tables.

If you change package classes or Composer autoloading, refresh Composer:

```bash
composer dump-autoload
```

## Main URLs

| URL | Purpose |
| --- | --- |
| `/` | Demo welcome page with SEO tags, breadcrumbs, image SEO, link SEO, and HTML sitemap output |
| `/sitemap.xml` | XML sitemap |
| `/robots.txt` | Robots file response |
| `/llms.txt` | LLM crawler information file |
| `/.well-known/security.txt` | Security contact file |
| `/seo-admin/generator` | PageForge bulk page generator admin UI |
| `/seo-admin/settings` | GUI settings manager for SEO modules |
| `/{slug}` | Displays generated SEO pages |

## Configuration

The package publishes a config file:

```text
config/seo.php
```

Most users do not need to edit this file manually for module control. Use the GUI Settings Manager at `/seo-admin/settings` to turn individual modules on or off from the database.

Important sections:

- `modules`: enable or disable package features such as meta tags, sitemap, redirects, schema, image SEO, and HTML minification.
- `defaults`: site name, title separator, default author, publisher, copyright, and default Open Graph image.
- `sitemap`: sitemap settings and model classes that should appear in the sitemap.
- `files`: content for robots, llms, and security text responses.
- `fallbacks`: fallback title and description templates.
- `verifications`: Google, Bing, Yandex, Pinterest, and Baidu verification codes.
- `organization`: global Organization and WebSite schema data.
- `scripts`: custom script output for the document head.
- `breadcrumbs`: breadcrumb labels, separator, and schema output.
- `links`: external link behavior.

You can also set verification values in `.env`:

```env
SEO_GOOGLE_VERIFICATION=your-google-code
SEO_BING_VERIFICATION=your-bing-code
```

## Admin Dashboard & GUI Settings Manager

The package includes a database-backed settings dashboard:

```text
/seo-admin/settings
```

Users no longer need to edit `config/seo.php` manually to enable or disable modules. The settings manager writes module preferences to the `seo_settings` table, and the service provider safely loads those values on boot.

Available module toggles:

- Meta Tags
- Sitemap
- Schema
- Redirects
- Image SEO
- Minification

These settings override `seo.modules.*` dynamically when the `seo_settings` table exists. The provider checks the table safely before querying it, so the app will not crash before installation.

## Adding SEO Tags To Layouts

Place the SEO component inside the `<head>` of your layout:

```blade
<x-seo::tags />
```

You can pass manual values:

```blade
<x-seo::tags
    title="About Us"
    description="Learn more about our company."
    keywords="company, about, services"
    canonical="{{ url('/about-us') }}"
    ogTitle="About Our Company"
    ogDescription="Learn more about our company."
    image="{{ asset('images/og-image.jpg') }}"
/>
```

You can also pass a model that uses the package SEO trait:

```blade
<x-seo::tags :model="$product" />
```

The component outputs:

- `<title>`
- meta description and keywords
- canonical URL
- Open Graph tags
- Twitter tags
- prev/next pagination links
- hreflang links
- site verification tags
- WebSite, Organization, page, and model schema JSON-LD
- custom head scripts from `config/seo.php`

## Using SEO On Eloquent Models

Add the trait to any model that needs SEO data:

```php
use Nirjon\LaravelSeo\Traits\HasSeo;

class Product extends Model
{
    use HasSeo;
}
```

Then save SEO data:

```php
$product->updateSeo([
    'meta_title' => 'Best Laptop Deals',
    'meta_description' => 'Find current laptop offers.',
    'meta_keywords' => 'laptop, deals, electronics',
    'canonical_url' => url('/products/best-laptop-deals'),
    'schema_type' => 'Product',
]);
```

The trait provides:

- `seo()` polymorphic relation
- `updateSeo(array $data)`
- `getSeoTitle()`
- `getSeoDescription()`
- `getSchema()`

## Sitemaps

The XML sitemap is available at:

```text
/sitemap.xml
```

To include a model in the sitemap, add it to `config/seo.php`:

```php
'sitemap' => [
    'models' => [
        \App\Models\TestProduct::class,
    ],
],
```

Each sitemap model should define `getSitemapUrl()`:

```php
public function getSitemapUrl()
{
    return url('/test-product/' . $this->slug);
}
```

The package also adds generated PageForge pages to the XML sitemap.

To display an HTML sitemap in a Blade view:

```blade
@htmlSitemap
```

## Breadcrumbs

The package registers a singleton breadcrumb service and a Blade component.

Example route:

```php
use Nirjon\LaravelSeo\Services\BreadcrumbService;

Route::get('/', function (BreadcrumbService $breadcrumbs) {
    $breadcrumbs->add('Products', url('/products'))
        ->add('Laptop', url('/products/laptop'))
        ->add('MacBook Pro');

    return view('welcome');
});
```

Render breadcrumbs in Blade:

```blade
<x-seo::breadcrumbs />
```

If enabled in config, the breadcrumb component also outputs BreadcrumbList JSON-LD schema.

## Static SEO Files

These routes are registered by the package:

```text
/robots.txt
/llms.txt
/.well-known/security.txt
```

Their content comes from `config/seo.php`.

## Redirects And 404 Logs

The package includes middleware for:

- active SEO redirects
- 404 logging
- automatic image alt/title/loading attributes
- external link controls
- feed/XML `X-Robots-Tag`
- HTML minification

404 records are stored in `seo_404_logs`.

Redirect records are stored in `seo_redirects`. The redirect middleware expects records with fields such as:

```text
source_url
destination_url
redirect_type
match_type
ignore_case
is_active
```

## PageForge Bulk Page Generator

Open the admin UI:

```text
/seo-admin/generator
```

The generator creates SEO landing pages from:

- a title template
- a slug template
- content HTML using a built-in Summernote Rich Text Editor
- meta title, description, and keywords
- optional featured image
- two keyword bundles
- optional author, publisher, copyright, and site name fields

Generated pages are stored in:

```text
seo_generated_pages
```

Generated pages are displayed by this root route in `routes/web.php`:

```php
Route::get('/{slug}', [\Nirjon\LaravelSeo\Http\Controllers\GeneratedPageController::class, 'show']);
```

Because this is a catch-all route, keep it after all other specific frontend routes.

## PageForge Placeholders

The admin UI currently supports two keyword bundles. Use these shortcodes in title, slug, content, and meta fields:

```text
{0}
{1}
```

`{0}` uses values from the first keyword bundle. `{1}` uses values from the second keyword bundle.

Example:

```text
Title: {Best|Top|Affordable} {0} repair in {1}
Slug: {0}-repair-{1}
Content: <p>We provide {fast|trusted|professional} {0} repair in {1}.</p>
```

With bundle values:

```text
Bundle 1: iPhone, Samsung
Bundle 2: Dhaka, Chittagong
```

The generator creates every keyword combination:

- iPhone repair Dhaka
- iPhone repair Chittagong
- Samsung repair Dhaka
- Samsung repair Chittagong

Spintax is supported with this format:

```text
{Best|Top|Reliable}
```

One option is selected randomly for each generated page.

## Queue Worker

PageForge dispatches `Nirjon\LaravelSeo\Jobs\GeneratePagesJob`, so a queue worker must be running unless your queue connection is `sync`.

For local development:

```bash
php artisan queue:work
```

If jobs do not appear to run, check:

- `QUEUE_CONNECTION` in `.env`
- whether `php artisan queue:work` is running
- failed jobs, if your app has failed job storage configured
- Laravel logs in `storage/logs`

## Uploaded Images

Featured images are stored on the public disk:

```text
storage/app/public/seo-images
```

Make sure the public storage link exists:

```bash
php artisan storage:link
```

## Publishing Package Views

To override package views in the application:

```bash
php artisan vendor:publish --tag=seo-views
```

Published views are copied to:

```text
resources/views/vendor/seo
```

## Development Notes

Useful commands:

```bash
composer install
composer dump-autoload
php artisan migrate
php artisan queue:work
php artisan serve
```

Clear Laravel caches after config or route changes:

```bash
php artisan optimize:clear
```

Package service provider:

```text
packages/nirjon/laravel-seo/src/SEOServiceProvider.php
```

Package routes:

```text
packages/nirjon/laravel-seo/routes/web.php
```

Root app routes:

```text
routes/web.php
```

## Troubleshooting

If SEO tags do not appear, confirm `<x-seo::tags />` is inside the page `<head>` and run `composer dump-autoload`.

If `/seo-admin/generator` opens but pages are not created, run a queue worker or set `QUEUE_CONNECTION=sync` for local testing.

If generated pages return 404, confirm the catch-all `/{slug}` route exists and is below other routes.

If uploaded images are not public, run `php artisan storage:link`.

If sitemap model URLs are missing, confirm the model is listed in `config/seo.php` and has a `getSitemapUrl()` method.

If config changes do not apply, run:

```bash
php artisan optimize:clear
```
