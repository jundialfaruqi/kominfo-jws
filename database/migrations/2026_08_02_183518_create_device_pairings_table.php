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
        Schema::create('device_pairings', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique()->comment('UUID unik dari perangkat TV');
            $table->string('pairing_code', 6)->unique()->comment('Kode 6 karakter untuk dipasangkan');
            $table->foreignId('profil_id')->nullable()->constrained('profils')->nullOnDelete();
            $table->enum('status', ['pending', 'linked'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_pairings');
    }
};
