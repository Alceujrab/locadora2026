<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->string('type', 30); // cnh, rg, comprovante_residencia, contrato_social
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'type']);
        });

        Schema::create('additional_drivers', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->string('name');
            $table->string('cpf', 14);
            $table->string('cnh_number', 20);
            $table->string('cnh_category', 5)->nullable();
            $table->date('cnh_expiry')->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_drivers');
        Schema::dropIfExists('customer_documents');
    }
};
