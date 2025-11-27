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
        Schema::create('prospect_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('opportunity_value', 12, 2)->nullable();
            $table->unsignedTinyInteger('probability')->nullable(); // 0-100
            $table->string('stage')->nullable(); // ex: novo, negociando, ganho, perdido
            $table->date('expected_close_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_private')->default(true);
            $table->timestamps();

            $table->unique('prospect_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_leads');
    }
};


