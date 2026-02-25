<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Users: dropar unique se existir
        $userUnique = collect(DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_email_unique'"));
        if ($userUnique->isNotEmpty()) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_email_unique');
            });
        }
        $userIndex = collect(DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_email_index'"));
        if ($userIndex->isEmpty()) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('email', 'users_email_index');
            });
        }

        // Customers: dropar unique se existir
        $custUnique = collect(DB::select("SHOW INDEX FROM customers WHERE Key_name = 'customers_cpf_cnpj_unique'"));
        if ($custUnique->isNotEmpty()) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropUnique('customers_cpf_cnpj_unique');
            });
        }
        $custIndex = collect(DB::select("SHOW INDEX FROM customers WHERE Key_name = 'customers_cpf_cnpj_index'"));
        if ($custIndex->isEmpty()) {
            Schema::table('customers', function (Blueprint $table) {
                $table->index('cpf_cnpj', 'customers_cpf_cnpj_index');
            });
        }
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
