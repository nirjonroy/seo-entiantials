<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nirjon_seo_keyword_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('nirjon_seo_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('nirjon_seo_keyword_bundles')->onDelete('cascade');
            $table->string('keyword');
            $table->timestamps();
        });

        Schema::create('nirjon_seo_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title_structure');
            $table->string('slug_structure');
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('nirjon_seo_template_bundles', function (Blueprint $table) {
            $table->foreignId('template_id')->constrained('nirjon_seo_templates')->onDelete('cascade');
            $table->foreignId('bundle_id')->constrained('nirjon_seo_keyword_bundles')->onDelete('cascade');
            $table->primary(['template_id', 'bundle_id']);
        });

        Schema::create('nirjon_seo_generated_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('nirjon_seo_templates')->onDelete('cascade');
            $table->string('url_slug')->unique();
            $table->string('final_title')->nullable();
            $table->text('final_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nirjon_seo_generated_pages');
        Schema::dropIfExists('nirjon_seo_template_bundles');
        Schema::dropIfExists('nirjon_seo_templates');
        Schema::dropIfExists('nirjon_seo_keywords');
        Schema::dropIfExists('nirjon_seo_keyword_bundles');
    }
};
