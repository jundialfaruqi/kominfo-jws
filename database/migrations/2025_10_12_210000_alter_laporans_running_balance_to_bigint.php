<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            // Ubah tipe data kolom running_balance menjadi BIGINT
            $table->bigInteger('running_balance')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            // Kembalikan tipe data kolom running_balance ke integer standar
            $table->integer('running_balance')->default(0)->change();
        });
    }
};