<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE service_orders MODIFY status VARCHAR(30) NOT NULL DEFAULT "aberta"');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE service_orders MODIFY status VARCHAR(20) NOT NULL DEFAULT "aberta"');
    }
};
