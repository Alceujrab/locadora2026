<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('doc_cnh')->nullable()->after('cnh_expiry')->comment('Arquivo CNH');
            $table->string('doc_cpf_cnpj_card')->nullable()->after('doc_cnh')->comment('Cartão CPF/CNPJ');
            $table->string('doc_address_proof')->nullable()->after('doc_cpf_cnpj_card')->comment('Comprovante de endereço');
            $table->string('doc_social_contract')->nullable()->after('doc_address_proof')->comment('Contrato social (PJ)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'doc_cnh',
                'doc_cpf_cnpj_card',
                'doc_address_proof',
                'doc_social_contract',
            ]);
        });
    }
};
