<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['pf', 'pj'])->default('pf');
            $table->string('name');
            $table->string('cpf_cnpj', 18)->unique();
            $table->string('rg', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            // CNH
            $table->string('cnh_number', 20)->nullable();
            $table->string('cnh_category', 5)->nullable();
            $table->date('cnh_expiry')->nullable();
            // PJ
            $table->string('company_name')->nullable();
            $table->string('state_registration', 20)->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_cpf', 14)->nullable();
            // Endereço
            $table->string('address_street')->nullable();
            $table->string('address_number', 10)->nullable();
            $table->string('address_complement')->nullable();
            $table->string('address_neighborhood')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state', 2)->nullable();
            $table->string('address_zip', 10)->nullable();
            // Contato de emergência
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relation')->nullable();
            // Controle
            $table->boolean('is_blocked')->default(false);
            $table->string('blocked_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'type']);
            $table->index('cpf_cnpj');
            $table->index('name');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
