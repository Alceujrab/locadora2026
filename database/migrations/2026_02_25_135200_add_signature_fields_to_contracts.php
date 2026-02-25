<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (! Schema::hasColumn('contracts', 'signature_image')) {
                $table->string('signature_image')->nullable()->after('signature_hash');
            }
            if (! Schema::hasColumn('contracts', 'signature_latitude')) {
                $table->decimal('signature_latitude', 10, 7)->nullable()->after('signature_image');
            }
            if (! Schema::hasColumn('contracts', 'signature_longitude')) {
                $table->decimal('signature_longitude', 10, 7)->nullable()->after('signature_latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            foreach (['signature_image', 'signature_latitude', 'signature_longitude'] as $col) {
                if (Schema::hasColumn('contracts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
