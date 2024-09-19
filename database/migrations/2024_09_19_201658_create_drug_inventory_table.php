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
        Schema::create('drug_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('drug_name'); // Drug name (e.g., Paracetamol)
            $table->string('drug_category'); // Drug category (e.g., Antibiotic)
            $table->string('dosage_form'); // Dosage form (e.g., Tablet, Liquid)
            $table->string('dosage_strength'); // Strength of the drug (e.g., 500 mg)
            $table->string('manufacturer'); // Manufacturer's name
            $table->string('batch_number'); // Batch number for tracking
            $table->date('expiry_date'); // Expiry date of the drug
            $table->integer('quantity_in_stock'); // Current stock level
            $table->integer('reorder_level'); // Reorder level threshold
            $table->integer('reorder_quantity'); // Quantity to reorder
            $table->string('storage_conditions'); // e.g., Refrigerated, Room temperature
            $table->decimal('price', 8, 2); // Price per unit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_inventory');
    }
};
