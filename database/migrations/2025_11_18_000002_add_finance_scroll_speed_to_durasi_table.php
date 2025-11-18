<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('durasi', function (Blueprint $table) {
            $table->decimal('finance_scroll_speed', 4, 2)->default(2.00)->after('final_isya');
        });
    }

    public function down(): void
    {
        Schema::table('durasi', function (Blueprint $table) {
            $table->dropColumn('finance_scroll_speed');
        });
    }
};