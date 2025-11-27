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
        Schema::table('user_searches', function (Blueprint $table) {
            $table->text('raw_data')->nullable()->after('nicho')->comment('Dados brutos da pesquisa (JSON)');
            $table->string('normalized_cidade')->nullable()->after('cidade')->comment('Cidade padronizada via Nominatim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_searches', function (Blueprint $table) {
            $table->dropColumn(['raw_data', 'normalized_cidade']);
        });
    }
};

