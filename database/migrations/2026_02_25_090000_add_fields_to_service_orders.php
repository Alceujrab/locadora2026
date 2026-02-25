<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('service_orders', 'requested_by')) {
                $table->string('requested_by')->nullable()->after('description');
            }
            if (! Schema::hasColumn('service_orders', 'vehicle_city')) {
                $table->string('vehicle_city')->nullable()->after('requested_by');
            }
            if (! Schema::hasColumn('service_orders', 'procedure_adopted')) {
                $table->text('procedure_adopted')->nullable()->after('vehicle_city');
            }
            if (! Schema::hasColumn('service_orders', 'driver_phone')) {
                $table->string('driver_phone', 20)->nullable()->after('procedure_adopted');
            }
            if (! Schema::hasColumn('service_orders', 'opened_by')) {
                $table->unsignedBigInteger('opened_by')->nullable()->after('driver_phone');
                $table->foreign('opened_by')->references('id')->on('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('service_orders', 'customer_id')) {
                $table->char('customer_id', 36)->nullable()->after('opened_by');
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            }
            if (! Schema::hasColumn('service_orders', 'attachments')) {
                $table->json('attachments')->nullable()->after('nf_path');
            }
            if (! Schema::hasColumn('service_orders', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('attachments');
            }
            if (! Schema::hasColumn('service_orders', 'signature_token')) {
                $table->string('signature_token', 40)->nullable()->after('pdf_path');
            }
            if (! Schema::hasColumn('service_orders', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signature_token');
            }
            if (! Schema::hasColumn('service_orders', 'signature_ip')) {
                $table->string('signature_ip', 45)->nullable()->after('signed_at');
            }
            if (! Schema::hasColumn('service_orders', 'signature_hash')) {
                $table->string('signature_hash')->nullable()->after('signature_ip');
            }
            if (! Schema::hasColumn('service_orders', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('signature_hash');
            }
            if (! Schema::hasColumn('service_orders', 'closing_notes')) {
                $table->text('closing_notes')->nullable()->after('closed_at');
            }
        });

        // Alterar enum de type para string mais flexível
        if (Schema::hasColumn('service_orders', 'type')) {
            Schema::table('service_orders', function (Blueprint $table) {
                $table->string('type', 30)->default('corretiva')->change();
            });
        }

        // Tabela de anotações
        if (! Schema::hasTable('service_order_notes')) {
            Schema::create('service_order_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->text('content');
                $table->timestamps();
                $table->index('service_order_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_order_notes');

        Schema::table('service_orders', function (Blueprint $table) {
            $cols = [
                'requested_by', 'vehicle_city', 'procedure_adopted', 'driver_phone',
                'attachments', 'pdf_path', 'signature_token', 'signed_at',
                'signature_ip', 'signature_hash', 'closed_at', 'closing_notes',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('service_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (Schema::hasColumn('service_orders', 'opened_by')) {
                $table->dropForeign(['opened_by']);
                $table->dropColumn('opened_by');
            }
            if (Schema::hasColumn('service_orders', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }
};
