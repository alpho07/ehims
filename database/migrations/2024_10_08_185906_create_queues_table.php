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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade'); // Clinic patient is currently in
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->integer('position');  // Position in the queue
            $table->string('status', 100)->default('waiting'); // Queue status (waiting, referred, etc.)
            $table->foreignId('referred_from_id')->nullable()->constrained('clinics')->onDelete('set null'); // Where they came from
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
