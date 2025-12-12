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
        Schema::create('sales_scripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('sales_script_categories')->onDelete('cascade');
            $table->enum('stage', [
                'introducao',
                'qualificacao',
                'levar_call',
                'quebra_objecao',
                'fechamento'
            ]);
            $table->string('title')->nullable();
            $table->text('content');
            $table->text('tips')->nullable(); // Dicas de como usar o script
            $table->integer('order')->default(0); // Ordem de exibição
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'stage', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_scripts');
    }
};

