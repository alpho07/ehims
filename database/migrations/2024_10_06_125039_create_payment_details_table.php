<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();

            // Foreign key to payments table
            $table->foreignId('payment_id')
                ->constrained()
                ->onDelete('cascade');  // If a payment is deleted, remove its details

            // Foreign key to payment_items table
            $table->foreignId('payment_item_id')
                ->constrained()
                ->onDelete('cascade');  // If a payment item is deleted, remove related details

            // Payment details
            $table->decimal('amount', 10, 2); // The amount being paid for this item
            $table->string('payment_type')->nullable(); // E.g., Out of Pocket, Insurance
            $table->string('payment_mode')->nullable(); // E.g., Card, Cash, Mobile Money
            $table->string('payment_reference')->nullable(); // E.g., Card, Cash, Mobile Money

            // Optional reference to insurance
            $table->foreignId('insurance_id')->nullable()
                ->constrained()
                ->onDelete('set null'); // If insurance is deleted, set this field to null

            // Co-pay information
            $table->boolean('is_copay')->default(false); // Whether it's a co-pay scenario
            $table->decimal('waiver_amount', 10, 2)->nullable(); // Amount waived

            // Total amount paid after adjustments (like waivers)
            $table->decimal('total_amount', 10, 2)->nullable();

            $table->integer('facility_id')->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');

            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
}
