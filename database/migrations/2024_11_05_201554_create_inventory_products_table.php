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
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable(); // Item name or type, e.g., "SPECTACLE LENSES (FEMALE)"
            $table->string('description')->nullable(); // Detailed description, e.g., "PC HC F 001 +0.50"
            $table->string('system_code')->unique()->nullable(); // System Code, e.g., "EM07FSL001"
            $table->string('type')->nullable(); // System Code, e.g., "EM07FSL001"
            $table->string('gender')->nullable(); // Gender: Male, Female, or Unisex
            $table->decimal('price', 8, 2)->nullable(); // Price with decimal precision
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
