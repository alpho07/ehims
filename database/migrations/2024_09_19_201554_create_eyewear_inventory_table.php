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
        Schema::create('eyewear_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('prescription');
            $table->decimal('pupillary_distance', 5, 2); // e.g., 60.00mm
            $table->string('lens_type');
            $table->string('lens_material');
            $table->string('lens_coating');
            $table->string('frame_style');
            $table->string('frame_material');
            $table->string('frame_color');
            $table->decimal('bridge_size', 5, 2); // e.g., 18.00mm
            $table->decimal('temple_length', 5, 2); // e.g., 140.00mm
            $table->decimal('lens_width', 5, 2); // e.g., 55.00mm
            $table->decimal('lens_height', 5, 2); // e.g., 40.00mm
            $table->string('gender');
            $table->string('brand');
            $table->string('frame_size');
            $table->decimal('weight', 5, 2); // e.g., 25.00 grams
            $table->string('uv_protection');
            $table->integer('stock_quantity')->unsigned();
            $table->integer('reorder_level')->unsigned();
            $table->integer('reorder_quantity')->unsigned();
            $table->text('storage_conditions');
            $table->decimal('price', 10, 2); // e.g., $199.99
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eyewear_inventory');
    }
};
