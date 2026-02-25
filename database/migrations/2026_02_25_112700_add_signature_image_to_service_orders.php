<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('service_orders', 'signature_image')) {
            Schema::table('service_orders', function (Blueprint $table) {
                $table->string('signature_image')->nullable()->after('signature_hash')
                    ->comment('Caminho da imagem PNG da assinatura desenhada');
            });
        }
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            if (Schema::hasColumn('service_orders', 'signature_image')) {
                $table->dropColumn('signature_image');
            }
        });
    }
};
