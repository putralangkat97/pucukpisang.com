<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('storage_path');
            $table->string('file_type');
            $table->integer('pages')->default(1);
            $table->string('status')->default('pending');
            $table->json('operations');
            $table->json('operation_params')->nullable();
            $table->json('result')->nullable();
            $table->string('ai_model')->default('deepseek');
            $table->integer('token_usage')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
