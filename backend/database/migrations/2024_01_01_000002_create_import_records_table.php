<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained()->onDelete('cascade');
            $table->integer('row_number');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender', 1)->nullable();
            $table->boolean('is_valid')->default(false);
            $table->json('errors')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_records');
    }
}; 