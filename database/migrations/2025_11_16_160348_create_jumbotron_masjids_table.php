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
        Schema::create('jumbotron_masjids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->constrained('profils')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('jumbotron_masjid_1')->nullable();
            $table->string('jumbotron_masjid_2')->nullable();
            $table->string('jumbotron_masjid_3')->nullable();
            $table->string('jumbotron_masjid_4')->nullable();
            $table->string('jumbotron_masjid_5')->nullable();
            $table->string('jumbotron_masjid_6')->nullable();
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumbotron_masjids');
    }
};
