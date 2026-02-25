<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_receivable', function (Blueprint $table) {
            if (! Schema::hasColumn('accounts_receivable', 'payment_proof_path')) {
                $table->string('payment_proof_path')->nullable()->after('payment_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts_receivable', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_receivable', 'payment_proof_path')) {
                $table->dropColumn('payment_proof_path');
            }
        });
    }
};
