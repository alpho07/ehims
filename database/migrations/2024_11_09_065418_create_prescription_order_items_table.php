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
        Schema::create('prescription_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_product_id')->constrained('hub_facility_inventories')->onDelete('cascade');
            $table->integer('available_stock');
            $table->integer('requested_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_order_items');
    }
};
