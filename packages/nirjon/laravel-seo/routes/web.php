<?php

use Illuminate\Support\Facades\Route;
use Nirjon\LaravelSeo\Http\Controllers\PageGeneratorController;

Route::middleware(['web'])->group(function () {
    Route::get('seo-admin/generator', [PageGeneratorController::class, 'index']);
    Route::get('seo-admin/generator/api-pages', [PageGeneratorController::class, 'apiGetPages']);
    Route::post('seo-admin/generator/api-generate', [PageGeneratorController::class, 'apiGenerate']);
});