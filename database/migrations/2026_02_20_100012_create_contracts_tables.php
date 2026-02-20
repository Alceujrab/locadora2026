<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->longText('content');
            $table->json('variables')->nullable()->comment('Lista de variáveis disponíveis');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('template_id')->nullable()->constrained('contract_templates')->nullOnDelete();
            $table->string('contract_number', 20)->unique();
            $table->dateTime('pickup_date');
            $table->dateTime('return_date');
            $table->dateTime('actual_return_date')->nullable();
            $table->integer('pickup_mileage')->default(0);
            $table->integer('return_mileage')->nullable();
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->integer('total_days')->default(1);
            $table->decimal('extras_total', 10, 2)->default(0);
            $table->decimal('caution_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('additional_charges', 10, 2)->default(0);
            $table->text('additional_charges_description')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status', 30)->default('rascunho');
            // Assinatura digital
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_token', 64)->nullable();
            $table->string('signature_ip', 45)->nullable();
            $table->string('signature_hash', 64)->nullable()->comment('SHA-256');
            $table->string('signature_method', 20)->nullable()->comment('sms, email, presencial');
            $table->string('pdf_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['vehicle_id', 'status']);
            $table->index('contract_number');
            $table->index('status');
        });

        Schema::create('contract_extras', function (Blueprint $table) {
            $table->id();
            $table->uuid('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreignId('rental_extra_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 8, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('contract_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index('contract_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_logs');
        Schema::dropIfExists('contract_extras');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_templates');
    }
};
