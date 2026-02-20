<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained();
            $table->uuid('contract_id')->nullable();
            $table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
            $table->enum('type', ['saida', 'retorno']);
            $table->foreignId('inspector_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('mileage')->default(0);
            $table->tinyInteger('fuel_level')->default(100)->comment('Percentual 0-100');
            $table->dateTime('inspection_date');
            $table->enum('overall_condition', ['excelente', 'bom', 'regular', 'ruim'])->default('bom');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pendente'); // pendente, concluida
            $table->timestamps();

            $table->index(['vehicle_id', 'type']);
            $table->index('contract_id');
        });

        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('vehicle_inspections')->cascadeOnDelete();
            $table->string('category', 30); // lataria, pneus, motor, interior, eletrica, acessorios
            $table->string('item_name');
            $table->enum('condition', ['bom', 'regular', 'ruim', 'danificado'])->default('bom');
            $table->text('damage_description')->nullable();
            $table->decimal('damage_value', 10, 2)->default(0);
            $table->json('photos')->nullable()->comment('Array de paths das fotos');
            $table->timestamps();

            $table->index(['inspection_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
        Schema::dropIfExists('vehicle_inspections');
    }
};
