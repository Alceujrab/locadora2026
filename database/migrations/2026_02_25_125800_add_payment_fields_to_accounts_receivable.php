<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_receivable', function (Blueprint $table) {
            if (! Schema::hasColumn('accounts_receivable', 'payment_method')) {
                $table->string('payment_method', 30)->nullable()->after('status');
            }
            if (! Schema::hasColumn('accounts_receivable', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('amount');
            }
            if (! Schema::hasColumn('accounts_receivable', 'payer_name')) {
                $table->string('payer_name')->nullable()->after('payment_method');
            }
            if (! Schema::hasColumn('accounts_receivable', 'payment_bank')) {
                $table->string('payment_bank')->nullable()->after('payer_name');
            }
            if (! Schema::hasColumn('accounts_receivable', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_bank');
            }
            if (! Schema::hasColumn('accounts_receivable', 'recurrence')) {
                $table->string('recurrence', 20)->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts_receivable', function (Blueprint $table) {
            foreach (['payment_method', 'paid_amount', 'payer_name', 'payment_bank', 'payment_reference', 'recurrence'] as $col) {
                if (Schema::hasColumn('accounts_receivable', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
