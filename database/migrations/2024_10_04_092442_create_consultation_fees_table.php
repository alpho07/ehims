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
        Schema::create('consultation_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_type_id')->constrained()->onDelete('cascade'); // Reference to ConsultationType model
            $table->decimal('fee_amount', 10, 2); // Base fee amount
            $table->boolean('is_active')->default(true); // Whether the fee is active
            $table->timestamps(); // Created at & Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_fees');
    }
};
