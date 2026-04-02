<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicle_inspections', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('status');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signature_token')) {
                $table->string('signature_token', 80)->nullable()->after('pdf_path');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signed_at')) {
                $table->dateTime('signed_at')->nullable()->after('signature_token');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signature_ip')) {
                $table->string('signature_ip', 45)->nullable()->after('signed_at');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signature_hash')) {
                $table->string('signature_hash')->nullable()->after('signature_ip');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signature_image')) {
                $table->string('signature_image')->nullable()->after('signature_hash');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signature_latitude')) {
                $table->decimal('signature_latitude', 10, 7)->nullable()->after('signature_image');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signature_longitude')) {
                $table->decimal('signature_longitude', 10, 7)->nullable()->after('signature_latitude');
            }
            if (! Schema::hasColumn('vehicle_inspections', 'signature_method')) {
                $table->string('signature_method', 50)->nullable()->after('signature_longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            foreach ([
                'pdf_path',
                'signature_token',
                'signed_at',
                'signature_ip',
                'signature_hash',
                'signature_image',
                'signature_latitude',
                'signature_longitude',
                'signature_method',
            ] as $column) {
                if (Schema::hasColumn('vehicle_inspections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};