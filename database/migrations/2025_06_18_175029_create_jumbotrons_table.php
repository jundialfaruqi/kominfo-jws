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
        Schema::create('jumbotrons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('jumbo1')->nullable();
            $table->string('jumbo2')->nullable();
            $table->string('jumbo3')->nullable();
            $table->string('jumbo4')->nullable();
            $table->string('jumbo5')->nullable();
            $table->string('jumbo6')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jumbotrons');
    }
};
