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
        Schema::table('device_pairings', function (Blueprint $table) {
            $table->string('device_brand')->nullable()->after('device_id')->comment('Merk perangkat TV, contoh: samsung');
            $table->string('device_model')->nullable()->after('device_brand')->comment('Model perangkat TV, contoh: SM-T510');
            $table->string('os_version')->nullable()->after('device_model')->comment('Versi OS, contoh: Android 12');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_pairings', function (Blueprint $table) {
            $table->dropColumn(['device_brand', 'device_model', 'os_version']);
        });
    }
};
