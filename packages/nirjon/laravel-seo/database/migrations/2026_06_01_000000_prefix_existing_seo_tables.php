<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables() as $oldTable => $newTable) {
            if (Schema::hasTable($oldTable) && !Schema::hasTable($newTable)) {
                Schema::rename($oldTable, $newTable);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_reverse($this->tables()) as $oldTable => $newTable) {
            if (Schema::hasTable($newTable) && !Schema::hasTable($oldTable)) {
                Schema::rename($newTable, $oldTable);
            }
        }
    }

    private function tables(): array
    {
        return [
            'seo_metas' => 'nirjon_seo_metas',
            'seo_redirects' => 'nirjon_seo_redirects',
            'seo_404_logs' => 'nirjon_seo_404_logs',
            'seo_data' => 'nirjon_seo_data',
            'seo_settings' => 'nirjon_seo_settings',
            'seo_keyword_bundles' => 'nirjon_seo_keyword_bundles',
            'seo_keywords' => 'nirjon_seo_keywords',
            'seo_templates' => 'nirjon_seo_templates',
            'seo_template_bundles' => 'nirjon_seo_template_bundles',
            'seo_generated_pages' => 'nirjon_seo_generated_pages',
        ];
    }
};
