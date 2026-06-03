<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nirjon_seo_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('nirjon_seo_templates', 'logo_image')) {
                $table->string('logo_image')->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'primary_color')) {
                $table->string('primary_color', 20)->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'accent_color')) {
                $table->string('accent_color', 20)->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'background_color')) {
                $table->string('background_color', 20)->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'text_color')) {
                $table->string('text_color', 20)->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'font_family')) {
                $table->string('font_family')->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'container_width')) {
                $table->string('container_width', 20)->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'custom_css')) {
                $table->text('custom_css')->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'header_css')) {
                $table->longText('header_css')->nullable();
            }

            if (! Schema::hasColumn('nirjon_seo_templates', 'header_js')) {
                $table->longText('header_js')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('nirjon_seo_templates', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('nirjon_seo_templates', 'logo_image') ? 'logo_image' : null,
                Schema::hasColumn('nirjon_seo_templates', 'primary_color') ? 'primary_color' : null,
                Schema::hasColumn('nirjon_seo_templates', 'accent_color') ? 'accent_color' : null,
                Schema::hasColumn('nirjon_seo_templates', 'background_color') ? 'background_color' : null,
                Schema::hasColumn('nirjon_seo_templates', 'text_color') ? 'text_color' : null,
                Schema::hasColumn('nirjon_seo_templates', 'font_family') ? 'font_family' : null,
                Schema::hasColumn('nirjon_seo_templates', 'container_width') ? 'container_width' : null,
                Schema::hasColumn('nirjon_seo_templates', 'custom_css') ? 'custom_css' : null,
                Schema::hasColumn('nirjon_seo_templates', 'header_css') ? 'header_css' : null,
                Schema::hasColumn('nirjon_seo_templates', 'header_js') ? 'header_js' : null,
            ]);

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
