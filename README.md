# Laravel Advanced SEO

An advanced, fully modular, and comprehensive SEO package for Laravel applications. Elevate your technical SEO without the hassle.

## 🚀 Features

This package is completely modular. You can toggle any of these features on or off in the configuration file:

- **Dynamic Meta Tags & Open Graph**: Automatically generate standard meta tags, Open Graph (Facebook/LinkedIn), and Twitter Cards.
- **Auto XML & HTML Sitemaps**: Dynamically generate sitemaps based on your Eloquent models and static routes.
- **JSON-LD Schema**: Built-in support for WebSite, Organization, and LocalBusiness structured data.
- **Static SEO Files**: Automatically handles `robots.txt`, `llms.txt`, and `.well-known/security.txt`.
- **Advanced Redirections**: Manage 301 (Moved Permanently), 302 (Found), 410 (Gone), and 451 (Unavailable for Legal Reasons) redirects effortlessly.
- **404 Error Monitor**: Automatically logs 404 Not Found errors so you can track broken links and fix them via redirections.
- **Auto Image SEO**: A smart middleware that automatically generates and injects `alt` tags into your `<img>` tags based on the image filename.
- **HTML Minifier**: Automatically minifies your HTML output on the fly, removing unnecessary whitespace and comments to improve page load speed while protecting essential tags like `<pre>`, `<textarea>`, `<script>`, and `<style>`.

## 📋 Requirements

- **PHP** >= 8.0
- **Laravel** >= 9.x

## 📦 Installation

You can install the package via Composer:

```bash
composer require nirjon/laravel-seo
```

After requiring the package, run the built-in installation command. This will publish the configuration file and execute the necessary database migrations (for Redirections and 404 Logs):

```bash
php artisan seo:install
```

## ⚙️ Configuration

The installation command publishes a configuration file at `config/seo.php`.

You can enable or disable individual modules inside this file to perfectly suit your application's needs:

```php
    /*
    |--------------------------------------------------------------------------
    | SEO Modules
    |--------------------------------------------------------------------------
    |
    | Here you can enable or disable specific modules within the package.
    | Set a module to 'true' to enable it, or 'false' to disable it.
    |
    */
    'modules' => [
        'meta'         => true,
        'sitemaps'     => true,
        'redirections' => true,
        'schema'       => true,
        'local_seo'    => true,
        'image_seo'    => true,
        'minify_html'  => true,
    ],
```

## 🛠️ Usage

To output the generated SEO tags, simply include the provided Blade component in the `<head>` section of your application's layout file (e.g., `resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Render SEO Tags here -->
    <x-seo::tags />
    
    <title>My Application</title>
</head>
<body>
    @yield('content')
</body>
</html>
```

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.