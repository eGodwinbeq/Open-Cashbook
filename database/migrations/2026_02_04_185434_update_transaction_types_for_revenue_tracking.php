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
        // SQLite doesn't support modifying enum constraints directly
        // We need to recreate the table with the new enum values
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['in', 'out', 'income', 'expense', 'expected_income'])->after('chapter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['in', 'out'])->after('chapter_id');
        });
    }
};
