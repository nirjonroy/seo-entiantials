<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nirjon_seo_generated_pages', function (Blueprint $table) {
            if (! Schema::hasColumn('nirjon_seo_generated_pages', 'replacement_values')) {
                $table->json('replacement_values')->nullable()->after('featured_image');
            }
        });
    }

    public function down()
    {
        Schema::table('nirjon_seo_generated_pages', function (Blueprint $table) {
            if (Schema::hasColumn('nirjon_seo_generated_pages', 'replacement_values')) {
                $table->dropColumn('replacement_values');
            }
        });
    }
};
