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
        // Add composite indexes for route model binding queries
        // These improve performance when looking up resources scoped to a user
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['id', 'user_id']);
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->index(['id', 'user_id']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->index(['id', 'invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['id', 'user_id']);
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex(['id', 'user_id']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex(['id', 'invoice_id']);
        });
    }
};

