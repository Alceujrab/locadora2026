<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ampliar status para 30 chars (já pode estar feito)
        DB::statement('ALTER TABLE service_orders MODIFY status VARCHAR(30) NOT NULL DEFAULT "aberta"');

        Schema::table('service_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('service_orders', 'customer_charge')) {
                $table->decimal('customer_charge', 10, 2)->default(0)->after('total')
                    ->comment('Valor cobrado do cliente (pode diferir do total)');
            }

            if (! Schema::hasColumn('service_orders', 'invoice_id')) {
                $table->uuid('invoice_id')->nullable()->after('customer_charge');
                $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
            }

            // Campos de assinatura de autorização (1ª)
            if (! Schema::hasColumn('service_orders', 'authorization_signed_at')) {
                $table->timestamp('authorization_signed_at')->nullable();
            }
            if (! Schema::hasColumn('service_orders', 'authorization_signature_image')) {
                $table->string('authorization_signature_image')->nullable();
            }
            if (! Schema::hasColumn('service_orders', 'authorization_ip')) {
                $table->string('authorization_ip', 45)->nullable();
            }

            // Campos de assinatura de conclusão (2ª)
            if (! Schema::hasColumn('service_orders', 'completion_signed_at')) {
                $table->timestamp('completion_signed_at')->nullable();
            }
            if (! Schema::hasColumn('service_orders', 'completion_signature_image')) {
                $table->string('completion_signature_image')->nullable();
            }
            if (! Schema::hasColumn('service_orders', 'completion_ip')) {
                $table->string('completion_ip', 45)->nullable();
            }
        });

        // Migrar dados: converter status antigo aguardando_assinatura -> aguardando_autorizacao
        DB::table('service_orders')
            ->where('status', 'aguardando_assinatura')
            ->update(['status' => 'aguardando_autorizacao']);
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $columns = [
                'customer_charge', 'authorization_signed_at', 'authorization_signature_image',
                'authorization_ip', 'completion_signed_at', 'completion_signature_image', 'completion_ip',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('service_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (Schema::hasColumn('service_orders', 'invoice_id')) {
                $table->dropForeign(['invoice_id']);
                $table->dropColumn('invoice_id');
            }
        });
    }
};
