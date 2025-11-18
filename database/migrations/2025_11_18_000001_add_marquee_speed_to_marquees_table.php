<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marquees', function (Blueprint $table) {
            $table->decimal('marquee_speed', 4, 2)->default(1.00)->after('marquee6');
        });
    }

    public function down(): void
    {
        Schema::table('marquees', function (Blueprint $table) {
            $table->dropColumn('marquee_speed');
        });
    }
};