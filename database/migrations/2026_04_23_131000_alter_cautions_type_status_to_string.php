<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Amplia os campos ENUM `type` e `status` da tabela `cautions` para VARCHAR,
     * permitindo novas modalidades (pix, cartao_credito, dinheiro, cheque,
     * promissoria) e novos status (executada) usados no painel.
     *
     * Causa raiz corrigida: SQLSTATE[01000] 1265 "Data truncated for column
     * 'type'" ao cadastrar caução com valores fora do ENUM original.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cautions')) {
            return;
        }

        // Usamos SQL cru pois a coluna original é ENUM e o doctrine/dbal
        // pode não estar disponível. Mantém dados existentes.
        DB::statement("ALTER TABLE `cautions` MODIFY `type` VARCHAR(30) NOT NULL DEFAULT 'cartao_credito'");
        DB::statement("ALTER TABLE `cautions` MODIFY `status` VARCHAR(30) NOT NULL DEFAULT 'retida'");

        // Normaliza valores legados para o novo vocabulário do painel.
        DB::table('cautions')->where('type', 'cartao')->update(['type' => 'cartao_credito']);
        DB::table('cautions')->where('type', 'deposito')->update(['type' => 'dinheiro']);
        DB::table('cautions')->whereIn('status', ['cobrada_parcial', 'cobrada_total'])->update(['status' => 'executada']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('cautions')) {
            return;
        }

        // Reverte valores novos para os antigos antes de voltar a ENUM.
        DB::table('cautions')->whereNotIn('type', ['cartao', 'deposito'])->update(['type' => 'cartao']);
        DB::table('cautions')->whereNotIn('status', ['retida', 'liberada', 'cobrada_parcial', 'cobrada_total'])->update(['status' => 'retida']);

        DB::statement("ALTER TABLE `cautions` MODIFY `type` ENUM('cartao','deposito') NOT NULL DEFAULT 'cartao'");
        DB::statement("ALTER TABLE `cautions` MODIFY `status` ENUM('retida','liberada','cobrada_parcial','cobrada_total') NOT NULL DEFAULT 'retida'");
    }
};
