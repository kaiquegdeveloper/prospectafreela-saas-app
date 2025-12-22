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
            $table->string('servico')->nullable()->after('nicho');
            $table->boolean('only_valid_email')->default(false)->after('servico');
            $table->boolean('only_valid_site')->default(false)->after('only_valid_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_searches', function (Blueprint $table) {
            $table->dropColumn(['servico', 'only_valid_email', 'only_valid_site']);
        });
    }
};

