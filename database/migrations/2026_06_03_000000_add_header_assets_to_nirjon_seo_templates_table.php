<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nirjon_seo_templates', function (Blueprint $table) {
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
                Schema::hasColumn('nirjon_seo_templates', 'header_css') ? 'header_css' : null,
                Schema::hasColumn('nirjon_seo_templates', 'header_js') ? 'header_js' : null,
            ]);

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
