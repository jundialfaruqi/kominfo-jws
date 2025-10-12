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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel profils sebagai pemilik/masjid
            $table->foreignId('id_masjid')
                ->constrained('profils')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Tanggal laporan
            $table->date('tanggal');
            // Uraian/deskripsi laporan
            $table->text('uraian');
            // Jenis transaksi: masuk atau keluar
            $table->enum('jenis', ['masuk', 'keluar']);
            // Saldo dalam bentuk desimal (misal: rupiah)
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
