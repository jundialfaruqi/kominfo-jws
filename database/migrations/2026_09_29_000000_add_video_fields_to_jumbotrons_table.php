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
        Schema::table('jumbotrons', function (Blueprint $table) {
            $table->string('media_type')->default('image')->after('user_id');
            $table->string('video_file')->nullable()->after('jumbo6');
            $table->boolean('has_audio')->default(false)->after('video_file');
            $table->integer('video_duration')->default(0)->after('has_audio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jumbotrons', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'video_file', 'has_audio', 'video_duration']);
        });
    }
};
