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
            $table->foreignId('triage_id')->constrained('triages')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->text('doctors_comments')->nullable();
            $table->text('prescription')->nullable();

            // Right Eye Prescription Fields
            $table->decimal('right_eye_distance_sphere', 5, 2)->nullable();
            $table->decimal('right_eye_distance_cylinder', 5, 2)->nullable();
            $table->integer('right_eye_distance_axis')->nullable();
            $table->decimal('right_eye_reading_sphere', 5, 2)->nullable();
            $table->decimal('right_eye_reading_cylinder', 5, 2)->nullable();
            $table->integer('right_eye_reading_axis')->nullable();

            // Left Eye Prescription Fields
            $table->decimal('left_eye_distance_sphere', 5, 2)->nullable();
            $table->decimal('left_eye_distance_cylinder', 5, 2)->nullable();
            $table->integer('left_eye_distance_axis')->nullable();
            $table->decimal('left_eye_reading_sphere', 5, 2)->nullable();
            $table->decimal('left_eye_reading_cylinder', 5, 2)->nullable();
            $table->integer('left_eye_reading_axis')->nullable();

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
