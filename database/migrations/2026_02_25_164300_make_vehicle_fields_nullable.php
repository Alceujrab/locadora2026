<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('color')->nullable()->change();
            $table->integer('doors')->nullable()->change();
            $table->integer('seats')->nullable()->change();
            $table->string('trunk_capacity')->nullable()->change();
            $table->integer('mileage')->nullable()->default(0)->change();
        });
    }

    public function down(): void {}
};
