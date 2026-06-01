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
        Schema::create('nirjon_seo_404_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('referer')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('hits')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nirjon_seo_404_logs');
    }
};
