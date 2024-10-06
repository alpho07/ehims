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
        Schema::create('payment_payment_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_item_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2); // Cost of the item during this payment
            $table->string('payment_type')->nullable(); // Out of Pocket or Insurance
            $table->string('out_of_pocket_option')->nullable(); // Full Payment, Waiver, Free
            $table->decimal('waiver_amount', 10, 2)->nullable(); // Waiver amount
            $table->decimal('total_amount', 10, 2)->nullable(); // Total amount after waiver
            $table->string('payment_mode')->nullable(); // Payment mode: Card, Mobile Money, Cash
            $table->foreignId('insurance_1')->nullable()->constrained('insurances'); // Primary Insurance
            $table->decimal('insurance_1_coverage', 10, 2)->nullable(); // Amount covered by Insurance 1
            $table->foreignId('insurance_2')->nullable()->constrained('insurances'); // Secondary Insurance
            $table->decimal('insurance_2_coverage', 10, 2)->nullable(); // Amount covered by Insurance 2
            $table->decimal('remaining_amount', 10, 2)->nullable(); // Out of pocket amount after insurance coverage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_payment_item');
    }
};
