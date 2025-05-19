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
        Schema::create('adzans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('adzan1')->nullable();
            $table->string('adzan2')->nullable();
            $table->string('adzan3')->nullable();
            $table->string('adzan4')->nullable();
            $table->string('adzan5')->nullable();
            $table->string('adzan6')->nullable();
            $table->string('adzan7')->nullable();
            $table->string('adzan8')->nullable();
            $table->string('adzan9')->nullable();
            $table->string('adzan10')->nullable();
            $table->string('adzan11')->nullable();
            $table->string('adzan12')->nullable();
            $table->string('adzan13')->nullable();
            $table->string('adzan14')->nullable();
            $table->string('adzan15')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adzans');
    }
};
