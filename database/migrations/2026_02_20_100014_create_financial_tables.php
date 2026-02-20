<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Faturas
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('contract_id')->nullable();
            $table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->string('invoice_number', 20)->unique();
            $table->date('due_date');
            $table->integer('installment_number')->default(1);
            $table->integer('total_installments')->default(1);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0)->comment('Multa 2%');
            $table->decimal('interest_amount', 10, 2)->default(0)->comment('Juros 1% a.m.');
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status', 20)->default('aberta');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->string('mp_payment_id')->nullable();
            // NFS-e
            $table->string('nfse_number', 30)->nullable();
            $table->string('nfse_xml_path')->nullable();
            $table->string('nfse_pdf_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['contract_id']);
            $table->index(['due_date', 'status']);
            $table->index('status');
        });

        // Itens da fatura
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        // Pagamentos
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method', 20); // pix, cartao, boleto, dinheiro, transferencia
            $table->string('mp_payment_id')->nullable();
            $table->string('mp_status', 30)->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('pix_qr_code')->nullable();
            $table->text('pix_qr_code_base64')->nullable();
            $table->string('boleto_url')->nullable();
            $table->string('boleto_barcode')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('mp_payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
