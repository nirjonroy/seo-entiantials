<?php

use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\Http\Controllers\PageGeneratorController;
use Nirjon\LaravelSeo\Http\Controllers\SeoSettingsController;

Route::middleware(['web'])->group(function () {
    Route::get('seo-admin/generator', [PageGeneratorController::class, 'index']);
    Route::get('seo-admin/generator/api-pages', [PageGeneratorController::class, 'apiGetPages']);
    Route::post('seo-admin/generator/api-generate', [PageGeneratorController::class, 'apiGenerate']);

    Route::get('seo-admin/settings', [SeoSettingsController::class, 'index']);
    Route::post('seo-admin/settings', [SeoSettingsController::class, 'update']);
});
