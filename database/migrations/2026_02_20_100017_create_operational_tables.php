<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Multas de trânsito
        Schema::create('fines_traffic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained();
            $table->uuid('contract_id')->nullable();
            $table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
            $table->uuid('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->string('fine_code', 20)->nullable();
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->date('fine_date');
            $table->date('due_date')->nullable();
            $table->date('notification_date')->nullable();
            $table->string('auto_infraction_number', 30)->nullable();
            $table->enum('status', ['pendente', 'transferida', 'paga', 'recurso', 'cancelada'])->default('pendente');
            $table->enum('responsibility', ['locadora', 'locatario'])->default('locatario');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'status']);
            $table->index(['contract_id']);
            $table->index(['customer_id', 'status']);
        });

        // Audit logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('model_type');
            $table->string('model_id', 36)->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('user_id');
            $table->index('created_at');
        });

        // Templates de notificação
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // confirmacao_reserva, contrato_assinatura, lembrete_pagamento, etc
            $table->enum('channel', ['email', 'whatsapp']);
            $table->string('subject')->nullable();
            $table->text('content');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'channel']);
        });

        // Chamados do cliente
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            $table->string('status', 20)->default('aberto'); // aberto, em_andamento, respondido, fechado
            $table->string('category', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
            $table->index('status');
        });

        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('fines_traffic');
    }
};
