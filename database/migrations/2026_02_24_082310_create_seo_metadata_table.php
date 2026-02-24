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
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique()->comment('A URL relativa ex: /, /frota, /contato');
            $table->string('title')->nullable()->comment('Título SEO');
            $table->string('description', 500)->nullable()->comment('Meta Description SEO');
            $table->string('keywords', 300)->nullable()->comment('Palavras-chave separadas por vírgula');
            $table->string('og_image')->nullable()->comment('Imagem para WhatsApp/Facebook OG');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
    }
};
