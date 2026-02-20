<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('vehicle_categories')->nullOnDelete();
            $table->string('plate', 10)->unique();
            $table->string('renavam', 20)->nullable()->unique();
            $table->string('chassis', 30)->nullable()->unique();
            $table->string('brand', 50);
            $table->string('model', 100);
            $table->year('year_manufacture');
            $table->year('year_model');
            $table->string('color', 30);
            $table->enum('fuel', ['gasolina', 'etanol', 'flex', 'diesel', 'eletrico', 'hibrido'])->default('flex');
            $table->enum('transmission', ['manual', 'automatico', 'cvt', 'automatizado'])->default('automatico');
            $table->tinyInteger('doors')->default(4);
            $table->tinyInteger('seats')->default(5);
            $table->integer('trunk_capacity')->nullable()->comment('Litros');
            $table->integer('mileage')->default(0);
            $table->string('status', 20)->default('disponivel');
            // Preços override (se null, usa da categoria)
            $table->decimal('daily_rate_override', 10, 2)->nullable();
            $table->decimal('weekly_rate_override', 10, 2)->nullable();
            $table->decimal('monthly_rate_override', 10, 2)->nullable();
            // Valores
            $table->decimal('insurance_value', 10, 2)->default(0);
            $table->decimal('fipe_value', 10, 2)->default(0);
            $table->decimal('purchase_value', 10, 2)->default(0);
            $table->date('purchase_date')->nullable();
            // Documentação
            $table->date('ipva_due_date')->nullable();
            $table->date('licensing_due_date')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            // Extras
            $table->boolean('is_featured')->default(false);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['brand', 'model']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
