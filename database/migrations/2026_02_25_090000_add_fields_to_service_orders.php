<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // Informações operacionais
            $table->string('requested_by')->nullable()->after('description')->comment('Quem solicitou o serviço');
            $table->string('vehicle_city')->nullable()->after('requested_by')->comment('Cidade do veículo');
            $table->text('procedure_adopted')->nullable()->after('vehicle_city')->comment('Procedimento adotado');
            $table->string('driver_phone', 20)->nullable()->after('procedure_adopted')->comment('Telefone do motorista');
            $table->foreignId('opened_by')->nullable()->after('driver_phone')->constrained('users')->nullOnDelete();
            $table->foreignUuid('customer_id')->nullable()->after('opened_by')->constrained('customers')->nullOnDelete();

            // Anexos (fotos/vídeos)
            $table->json('attachments')->nullable()->after('nf_path')->comment('Fotos e vídeos do problema');

            // PDF
            $table->string('pdf_path')->nullable()->after('attachments');

            // Assinatura digital
            $table->string('signature_token', 40)->nullable()->after('pdf_path');
            $table->timestamp('signed_at')->nullable()->after('signature_token');
            $table->string('signature_ip', 45)->nullable()->after('signed_at');
            $table->string('signature_hash')->nullable()->after('signature_ip');

            // Fechamento
            $table->timestamp('closed_at')->nullable()->after('signature_hash');
            $table->text('closing_notes')->nullable()->after('closed_at');
        });

        // Alterar enum de type para incluir novos tipos
        Schema::table('service_orders', function (Blueprint $table) {
            $table->string('type', 30)->default('corretiva')->change();
        });

        // Tabela de anotações em timeline
        Schema::create('service_order_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->index('service_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_order_notes');

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['opened_by']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn([
                'requested_by', 'vehicle_city', 'procedure_adopted', 'driver_phone',
                'opened_by', 'customer_id', 'attachments', 'pdf_path',
                'signature_token', 'signed_at', 'signature_ip', 'signature_hash',
                'closed_at', 'closing_notes',
            ]);
        });
    }
};
