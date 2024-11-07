<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->foreignId('triage_id')->constrained('triages')->onDelete('cascade');
            $table->json('form_data')->nullable();  // Store dynamic clinic-specific data
            $table->foreignId('referred_to_id')->nullable()->constrained('clinics')->onDelete('set null');
            $table->string('reason_for_referral')->nullable();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade')->onDelete('set null');
            $table->integer('facility_id')->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
}
