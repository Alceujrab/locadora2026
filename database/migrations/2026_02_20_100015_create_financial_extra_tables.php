<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Caução
        Schema::create('cautions', function (Blueprint $table) {
            $table->id();
            $table->uuid('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->enum('type', ['cartao', 'deposito'])->default('cartao');
            $table->decimal('amount', 10, 2);
            $table->string('mp_payment_id')->nullable();
            $table->string('mp_preauth_id')->nullable()->comment('ID pré-autorização Mercado Pago');
            $table->enum('status', ['retida', 'liberada', 'cobrada_parcial', 'cobrada_total'])->default('retida');
            $table->timestamp('released_at')->nullable();
            $table->decimal('charged_amount', 10, 2)->default(0);
            $table->text('charge_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['contract_id', 'status']);
        });

        // Contas a pagar
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 30); // oficina, seguro, ipva, financiamento, aluguel, outros
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->string('status', 20)->default('pendente'); // pendente, paga, vencida, cancelada
            $table->enum('recurrence', ['unica', 'mensal', 'anual'])->default('unica');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status', 'due_date']);
            $table->index(['vehicle_id']);
        });

        // Contas a receber (projeção)
        Schema::create('accounts_receivable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->uuid('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->timestamp('received_at')->nullable();
            $table->string('status', 20)->default('pendente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status', 'due_date']);
        });

        // Fluxo de caixa
        Schema::create('cash_flow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['receita', 'despesa']);
            $table->string('category', 50);
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->boolean('is_projected')->default(false);
            $table->string('reference_type')->nullable()->comment('Model class');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'date', 'type']);
            $table->index(['date', 'is_projected']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flow');
        Schema::dropIfExists('accounts_receivable');
        Schema::dropIfExists('accounts_payable');
        Schema::dropIfExists('cautions');
    }
};
