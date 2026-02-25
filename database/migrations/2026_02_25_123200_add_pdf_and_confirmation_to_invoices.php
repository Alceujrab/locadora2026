<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('invoices', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('pdf_path');
            }
            if (! Schema::hasColumn('invoices', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('sent_at');
            }
            if (! Schema::hasColumn('invoices', 'confirmation_ip')) {
                $table->string('confirmation_ip', 45)->nullable()->after('confirmed_at');
            }
            if (! Schema::hasColumn('invoices', 'confirmation_token')) {
                $table->string('confirmation_token', 60)->nullable()->after('confirmation_ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            foreach (['pdf_path', 'sent_at', 'confirmed_at', 'confirmation_ip', 'confirmation_token'] as $col) {
                if (Schema::hasColumn('invoices', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
