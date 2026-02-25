<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // customer_id pode ter sido criada como bigint na migration parcial anterior
        // Precisamos garantir que é char(36) para UUID
        if (Schema::hasColumn('service_orders', 'customer_id')) {
            // Dropar FK se existir
            try {
                Schema::table('service_orders', function (Blueprint $table) {
                    $table->dropForeign(['customer_id']);
                });
            } catch (\Exception $e) {
                // FK pode não existir
            }

            // Alterar para char(36) para UUID
            DB::statement('ALTER TABLE service_orders MODIFY customer_id CHAR(36) NULL');

            // Recriar FK
            Schema::table('service_orders', function (Blueprint $table) {
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // noop
    }
};
