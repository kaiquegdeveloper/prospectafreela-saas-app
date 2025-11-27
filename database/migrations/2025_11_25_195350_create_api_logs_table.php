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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('api_name')->default('google_maps_places'); // Para futuras APIs
            $table->string('endpoint')->nullable();
            $table->string('method')->default('GET');
            $table->integer('status_code')->nullable();
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->decimal('cost', 10, 4)->default(0)->comment('Custo estimado em USD');
            $table->integer('response_time_ms')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['api_name', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
