<?php

namespace Nirjon\LaravelSeo;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\View\Components\SeoTags;
use Nirjon\LaravelSeo\Http\Controllers\SitemapController;

class SEOServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/seo.php', 'seo'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'seo');

        Blade::component('seo::tags', SeoTags::class);

        Route::get('/sitemap.xml', [SitemapController::class, 'index']);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/seo.php' => config_path('seo.php'),
            ], 'seo-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/seo'),
            ], 'seo-views');
        }
    }
}
