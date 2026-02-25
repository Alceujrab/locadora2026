<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tornar TODOS os campos opcionais nullable de uma vez
        // Só plate e status permanecem obrigatórios
        DB::statement("ALTER TABLE vehicles
            MODIFY brand VARCHAR(50) NULL,
            MODIFY model VARCHAR(100) NULL,
            MODIFY year_manufacture YEAR NULL,
            MODIFY year_model YEAR NULL,
            MODIFY color VARCHAR(30) NULL,
            MODIFY fuel ENUM('gasolina','etanol','flex','diesel','eletrico','hibrido') NULL DEFAULT 'flex',
            MODIFY transmission ENUM('manual','automatico','cvt','automatizado') NULL DEFAULT 'automatico',
            MODIFY doors TINYINT NULL DEFAULT 4,
            MODIFY seats TINYINT NULL DEFAULT 5,
            MODIFY mileage INT NULL DEFAULT 0,
            MODIFY insurance_value DECIMAL(10,2) NULL DEFAULT 0,
            MODIFY fipe_value DECIMAL(10,2) NULL DEFAULT 0,
            MODIFY purchase_value DECIMAL(10,2) NULL DEFAULT 0
        ");
    }

    public function down(): void {}
};
