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
        Schema::table('nirjon_seo_templates', function (Blueprint $table) {
            $table->string('featured_image')->nullable();
        });

        Schema::table('nirjon_seo_generated_pages', function (Blueprint $table) {
            $table->string('featured_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nirjon_seo_generated_pages', function (Blueprint $table) {
            $table->dropColumn('featured_image');
        });

        Schema::table('nirjon_seo_templates', function (Blueprint $table) {
            $table->dropColumn('featured_image');
        });
    }
};
