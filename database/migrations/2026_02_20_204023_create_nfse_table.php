<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfse', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
            $table->string('numero')->nullable()->unique();
            $table->string('serie')->default('A1');
            $table->date('data_emissao');
            $table->decimal('valor_servico', 12, 2);
            $table->decimal('aliquota_iss', 5, 2)->default(5.00);
            $table->decimal('valor_iss', 12, 2)->default(0);
            $table->string('codigo_servico')->nullable()->comment('Código CNAE/LC 116');
            $table->text('discriminacao')->comment('Descrição do serviço');
            $table->string('tomador_cnpj_cpf');
            $table->string('tomador_nome');
            $table->string('tomador_endereco')->nullable();
            $table->string('tomador_email')->nullable();
            $table->enum('status', ['rascunho', 'emitida', 'cancelada'])->default('rascunho');
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfse');
    }
};
