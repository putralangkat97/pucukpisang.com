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
        Schema::create('audios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('status')->default(0);
            $table->string('source_type');
            $table->text('source_path');
            $table->json('options');
            $table->string('ai_model')->nullable();
            $table->longText('transcript')->nullable();
            $table->longText('summary')->nullable();
            $table->longText('translations')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audios');
    }
};
