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
        Schema::create('durasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('adzan_shubuh')->default(4);
            $table->integer('iqomah_shubuh')->default(10);
            $table->integer('final_shubuh')->default(30);
            $table->integer('adzan_dzuhur')->default(4);
            $table->integer('iqomah_dzuhur')->default(10);
            $table->integer('final_dzuhur')->default(30);
            $table->integer('jumat_slide')->default(20);
            $table->integer('adzan_ashar')->default(4);
            $table->integer('iqomah_ashar')->default(10);
            $table->integer('final_ashar')->default(30);
            $table->integer('adzan_maghrib')->default(4);
            $table->integer('iqomah_maghrib')->default(10);
            $table->integer('final_maghrib')->default(30);
            $table->integer('adzan_isya')->default(4);
            $table->integer('iqomah_isya')->default(10);
            $table->integer('final_isya')->default(30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('durasi');
    }
};
