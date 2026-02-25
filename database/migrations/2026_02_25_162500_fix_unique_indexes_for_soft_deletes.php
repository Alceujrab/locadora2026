<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Users: trocar unique absoluto por validacao no app (soft deletes)
        Schema::table('users', function (Blueprint $table) {
            // Dropar unique index do email (permite re-cadastrar email de user deletado)
            try {
                $table->dropUnique('users_email_unique');
            } catch (\Exception $e) {
                // Index pode nao existir
            }
            // Adicionar index normal (nao unique) para performance
            try {
                $table->index('email', 'users_email_index');
            } catch (\Exception $e) {}
        });

        // Customers: trocar unique absoluto por validacao no app
        Schema::table('customers', function (Blueprint $table) {
            try {
                $table->dropUnique('customers_cpf_cnpj_unique');
            } catch (\Exception $e) {}
            try {
                $table->index('cpf_cnpj', 'customers_cpf_cnpj_index');
            } catch (\Exception $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try { $table->dropIndex('users_email_index'); } catch (\Exception $e) {}
            try { $table->unique('email'); } catch (\Exception $e) {}
        });
        Schema::table('customers', function (Blueprint $table) {
            try { $table->dropIndex('customers_cpf_cnpj_index'); } catch (\Exception $e) {}
            try { $table->unique('cpf_cnpj'); } catch (\Exception $e) {}
        });
    }
};
