<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('filename');
            $table->integer('position')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->timestamps();

            $table->index(['vehicle_id', 'position']);
        });

        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50); // crlv, seguro, laudo, etc.
            $table->string('number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'type']);
        });

        Schema::create('vehicle_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_included')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_accessories');
        Schema::dropIfExists('vehicle_documents');
        Schema::dropIfExists('vehicle_photos');
    }
};
