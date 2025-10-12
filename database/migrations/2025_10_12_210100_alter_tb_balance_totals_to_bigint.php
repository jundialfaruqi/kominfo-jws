<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_balance', function (Blueprint $table) {
            // Ubah tipe data kolom total_masuk, total_keluar, ending_balance menjadi BIGINT
            $table->bigInteger('total_masuk')->default(0)->change();
            $table->bigInteger('total_keluar')->default(0)->change();
            $table->bigInteger('ending_balance')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tb_balance', function (Blueprint $table) {
            // Kembalikan tipe data kolom ke DECIMAL(15,2) seperti awal
            $table->decimal('total_masuk', 15, 2)->default(0)->change();
            $table->decimal('total_keluar', 15, 2)->default(0)->change();
            $table->decimal('ending_balance', 15, 2)->default(0)->change();
        });
    }
};