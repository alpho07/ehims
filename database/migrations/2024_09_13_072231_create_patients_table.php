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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('file_number')->unique(); // Unique file number
            $table->string('hospital_number')->unique(); // UMR or unique hospital number
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->date('dob')->nullable(); // Date of birth for calculating age
            $table->string('gender');
            $table->string('phone')->nullable();
            $table->enum('source', ['Walk-In', 'Appointment', 'Referral']);
            $table->string('referral_facility')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
