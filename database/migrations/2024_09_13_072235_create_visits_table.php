<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            // Foreign key to patients table
            $table->foreignId('patient_id')
                ->constrained()
                ->onDelete('cascade');

            // Foreign key to clinics table (current clinic)
            $table->foreignId('clinic_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            // Timestamps for visit start and end
            $table->timestamp('visit_start_time')->useCurrent();
            $table->timestamp('visit_end_time')->nullable();

            // Status of the visit
            $table->string('status')->default('active');

            // Foreign keys for referrals
            $table->foreignId('referred_from_id')
                ->nullable()
                ->constrained('clinics')
                ->onDelete('set null');

            $table->foreignId('referred_to_id')
                ->nullable()
                ->constrained('clinics')
                ->onDelete('set null');

            // JSON fields for tracking previous clinics and staff seen
            $table->json('previous_clinics')->nullable();
            $table->json('staff_seen')->nullable();

            $table->integer('facility_id')->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('visits');
    }
}
