<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nirjon_seo_metas', function (Blueprint $table) {
            if (! Schema::hasColumn('nirjon_seo_metas', 'url_path')) {
                $table->string('url_path')->nullable()->after('seoable_id')->index();
            }

            if (! Schema::hasColumn('nirjon_seo_metas', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('robots_tag');
            }

            if (! Schema::hasColumn('nirjon_seo_metas', 'og_title')) {
                $table->string('og_title')->nullable()->after('og_image');
            }

            if (! Schema::hasColumn('nirjon_seo_metas', 'og_description')) {
                $table->text('og_description')->nullable()->after('og_title');
            }

            if (! Schema::hasColumn('nirjon_seo_metas', 'twitter_title')) {
                $table->string('twitter_title')->nullable()->after('og_description');
            }

            if (! Schema::hasColumn('nirjon_seo_metas', 'twitter_description')) {
                $table->text('twitter_description')->nullable()->after('twitter_title');
            }

            if (! Schema::hasColumn('nirjon_seo_metas', 'twitter_image')) {
                $table->string('twitter_image')->nullable()->after('twitter_description');
            }

            if (! Schema::hasColumn('nirjon_seo_metas', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('twitter_image')->index();
            }
        });
    }

    public function down()
    {
        Schema::table('nirjon_seo_metas', function (Blueprint $table) {
            $columns = [
                'url_path',
                'canonical_url',
                'og_title',
                'og_description',
                'twitter_title',
                'twitter_description',
                'twitter_image',
                'is_active',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('nirjon_seo_metas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
