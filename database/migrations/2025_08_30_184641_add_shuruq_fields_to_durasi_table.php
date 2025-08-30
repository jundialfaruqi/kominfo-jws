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
        Schema::table('durasi', function (Blueprint $table) {
            $table->integer('adzan_shuruq')->default(4)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('durasi', function (Blueprint $table) {
            $table->dropColumn('iqomah_shuruq');
            $table->dropColumn('final_shuruq');
        });
    }
};
