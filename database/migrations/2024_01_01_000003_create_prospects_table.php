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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('site')->nullable();
            $table->text('endereco')->nullable();
            $table->string('cidade');
            $table->string('nicho');
            $table->text('google_maps_url')->nullable();
            $table->enum('status', ['pending', 'done', 'error'])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['cidade', 'nicho']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};

