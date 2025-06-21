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
            $table->uuid('id')->primary();
            $table->integer('status')->default(0);
            $table->json('options');
            $table->string('file');
            $table->string('type')->default('pdf');
            $table->string('ai_model')->default('gemini');
            $table->longText('text_extraction')->nullable();
            $table->longText('summary')->nullable();
            $table->longText('translations')->nullable();
            $table->longText('error')->nullable();
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
