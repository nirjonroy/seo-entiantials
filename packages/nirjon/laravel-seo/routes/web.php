<?php

use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\Http\Controllers\GeneratedPageController;
use Nirjon\LaravelSeo\Http\Controllers\PageGeneratorController;
use Nirjon\LaravelSeo\Http\Controllers\SeoSettingsController;
use Nirjon\LaravelSeo\Http\Middleware\EnsureSeoAdminAuthenticated;

$adminMiddleware = array_values(array_unique(array_merge(
    (array) config('seo.admin.middleware', ['web', 'auth']),
    [EnsureSeoAdminAuthenticated::class]
)));

Route::middleware($adminMiddleware)->prefix('admin/seo-admin')->group(function () {
    Route::get('generator', [PageGeneratorController::class, 'index'])->name('seo.generator');
    Route::get('generator/api-pages', [PageGeneratorController::class, 'apiGetPages'])->name('seo.generator.apiPages');
    Route::get('generator/api-pages/{page}', [PageGeneratorController::class, 'apiShowPage'])->name('seo.generator.apiShowPage');
    Route::post('generator/api-generate', [PageGeneratorController::class, 'apiGenerate'])->name('seo.generator.apiGenerate');
    Route::delete('generator/api-pages/{page}', [PageGeneratorController::class, 'destroy'])->name('seo.generator.pages.destroy');

    Route::get('settings', [SeoSettingsController::class, 'index'])->name('seo.settings');
    Route::post('settings', [SeoSettingsController::class, 'update'])->name('seo.settings.update');
});

Route::middleware(['web'])->get('seo-media/{path}', [GeneratedPageController::class, 'media'])
    ->where('path', '.*')
    ->name('seo.media');

Route::middleware(['web'])->get('{slug}', [GeneratedPageController::class, 'show'])
    ->where('slug', '^(?!admin(?:/|$)|api(?:/|$)|seo-media(?:/|$)|storage(?:/|$)|uploads(?:/|$)|assets(?:/|$)|css(?:/|$)|js(?:/|$)|images(?:/|$)|sitemap\.xml$|robots\.txt$|llms\.txt$).+');
