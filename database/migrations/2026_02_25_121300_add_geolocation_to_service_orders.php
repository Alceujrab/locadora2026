<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('service_orders', 'authorization_latitude')) {
                $table->decimal('authorization_latitude', 10, 7)->nullable()->after('authorization_ip');
                $table->decimal('authorization_longitude', 10, 7)->nullable()->after('authorization_latitude');
            }
            if (! Schema::hasColumn('service_orders', 'completion_latitude')) {
                $table->decimal('completion_latitude', 10, 7)->nullable()->after('completion_ip');
                $table->decimal('completion_longitude', 10, 7)->nullable()->after('completion_latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            foreach (['authorization_latitude', 'authorization_longitude', 'completion_latitude', 'completion_longitude'] as $col) {
                if (Schema::hasColumn('service_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
