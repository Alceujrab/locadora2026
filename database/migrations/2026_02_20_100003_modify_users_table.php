<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('whatsapp', 20)->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('whatsapp');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->softDeletes();

            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['branch_id', 'phone', 'whatsapp', 'avatar', 'is_active']);
            $table->dropSoftDeletes();
        });
    }
};
