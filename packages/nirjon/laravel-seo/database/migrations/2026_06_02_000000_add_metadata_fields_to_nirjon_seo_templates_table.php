<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nirjon_seo_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('nirjon_seo_templates', 'meta_image')) {
                $table->string('meta_image')->nullable();
            }

            if (!Schema::hasColumn('nirjon_seo_templates', 'author')) {
                $table->string('author')->nullable();
            }

            if (!Schema::hasColumn('nirjon_seo_templates', 'publisher')) {
                $table->string('publisher')->nullable();
            }

            if (!Schema::hasColumn('nirjon_seo_templates', 'copyright')) {
                $table->string('copyright')->nullable();
            }

            if (!Schema::hasColumn('nirjon_seo_templates', 'site_name')) {
                $table->string('site_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nirjon_seo_templates', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('nirjon_seo_templates', 'meta_image') ? 'meta_image' : null,
                Schema::hasColumn('nirjon_seo_templates', 'author') ? 'author' : null,
                Schema::hasColumn('nirjon_seo_templates', 'publisher') ? 'publisher' : null,
                Schema::hasColumn('nirjon_seo_templates', 'copyright') ? 'copyright' : null,
                Schema::hasColumn('nirjon_seo_templates', 'site_name') ? 'site_name' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
