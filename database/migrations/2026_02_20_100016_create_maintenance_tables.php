<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ordens de Serviço
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['preventiva', 'corretiva'])->default('corretiva');
            $table->string('description');
            $table->decimal('items_total', 10, 2)->default(0);
            $table->decimal('labor_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status', 20)->default('aberta');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->string('nf_number', 30)->nullable();
            $table->string('nf_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['vehicle_id', 'status']);
        });

        // Itens da OS
        Schema::create('service_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['peca', 'mao_de_obra']);
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        // Alertas de manutenção
        Schema::create('maintenance_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['km', 'tempo']);
            $table->string('description');
            $table->integer('trigger_km')->nullable()->comment('Km para disparo');
            $table->integer('trigger_days')->nullable()->comment('Dias para disparo');
            $table->date('last_service_date')->nullable();
            $table->integer('last_service_km')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_alerts');
        Schema::dropIfExists('service_order_items');
        Schema::dropIfExists('service_orders');
    }
};
