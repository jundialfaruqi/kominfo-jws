<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change saldo column from DECIMAL(15,2) to BIGINT
        DB::statement('ALTER TABLE laporans MODIFY saldo BIGINT NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert saldo column back to DECIMAL(15,2)
        DB::statement('ALTER TABLE laporans MODIFY saldo DECIMAL(15,2) NOT NULL');
    }
};