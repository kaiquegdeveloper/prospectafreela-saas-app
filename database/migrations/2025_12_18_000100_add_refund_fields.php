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
        Schema::table('user_payments', function (Blueprint $table) {
            $table->boolean('refunded')->default(false)->after('notes');
            $table->timestamp('refunded_at')->nullable()->after('refunded');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_payments', function (Blueprint $table) {
            $table->dropColumn(['refunded', 'refunded_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['refunded_at']);
        });
    }
};

