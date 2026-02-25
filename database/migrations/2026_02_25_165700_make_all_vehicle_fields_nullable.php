<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Todos os campos opcionais nullable de uma vez
            $table->string('chassis')->nullable()->change();
            $table->string('renavam')->nullable()->change();
            $table->string('fuel')->nullable()->change();
            $table->string('transmission')->nullable()->change();
            $table->date('ipva_due_date')->nullable()->change();
            $table->date('licensing_due_date')->nullable()->change();
            $table->date('insurance_expiry_date')->nullable()->change();
            $table->decimal('daily_rate_override', 10, 2)->nullable()->change();
            $table->decimal('weekly_rate_override', 10, 2)->nullable()->change();
            $table->decimal('monthly_rate_override', 10, 2)->nullable()->change();
            $table->decimal('purchase_value', 10, 2)->nullable()->change();
            $table->decimal('fipe_value', 10, 2)->nullable()->change();
            $table->decimal('insurance_value', 10, 2)->nullable()->change();
            $table->date('purchase_date')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->text('notes')->nullable()->change();
        });
    }

    public function down(): void {}
};
