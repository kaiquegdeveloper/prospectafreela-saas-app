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
        Schema::create('queue_pauses', function (Blueprint $table) {
            $table->id();
            $table->string('queue_name')->nullable(); // null = pausa global, 'prospecting' = fila específica
            $table->boolean('is_paused')->default(false);
            $table->text('reason')->nullable();
            $table->foreignId('paused_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->timestamps();

            $table->unique('queue_name');
            $table->index('is_paused');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_pauses');
    }
};

