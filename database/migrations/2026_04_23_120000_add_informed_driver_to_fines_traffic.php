<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fines_traffic', function (Blueprint $table) {
            $table->string('driver_name', 150)->nullable()->after('customer_id');
            $table->string('driver_cpf', 20)->nullable()->after('driver_name');
            $table->string('driver_rg', 30)->nullable()->after('driver_cpf');
            $table->string('driver_phone', 30)->nullable()->after('driver_rg');
            $table->string('driver_email', 150)->nullable()->after('driver_phone');
            $table->string('driver_cnh_number', 30)->nullable()->after('driver_email');
            $table->date('driver_cnh_expires_at')->nullable()->after('driver_cnh_number');
            $table->string('driver_zipcode', 15)->nullable()->after('driver_cnh_expires_at');
            $table->string('driver_address', 255)->nullable()->after('driver_zipcode');
            $table->string('driver_address_number', 20)->nullable()->after('driver_address');
            $table->string('driver_address_complement', 100)->nullable()->after('driver_address_number');
            $table->string('driver_neighborhood', 120)->nullable()->after('driver_address_complement');
            $table->string('driver_city', 120)->nullable()->after('driver_neighborhood');
            $table->string('driver_state', 2)->nullable()->after('driver_city');
            $table->string('driver_cnh_path', 500)->nullable()->after('driver_state');
            $table->string('driver_address_proof_path', 500)->nullable()->after('driver_cnh_path');

            $table->index('driver_cpf');
        });
    }

    public function down(): void
    {
        Schema::table('fines_traffic', function (Blueprint $table) {
            $table->dropIndex(['driver_cpf']);
            $table->dropColumn([
                'driver_name',
                'driver_cpf',
                'driver_rg',
                'driver_phone',
                'driver_email',
                'driver_cnh_number',
                'driver_cnh_expires_at',
                'driver_zipcode',
                'driver_address',
                'driver_address_number',
                'driver_address_complement',
                'driver_neighborhood',
                'driver_city',
                'driver_state',
                'driver_cnh_path',
                'driver_address_proof_path',
            ]);
        });
    }
};
