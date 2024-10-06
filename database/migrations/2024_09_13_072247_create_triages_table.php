<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('triages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->integer('age')->nullable();
            $table->decimal('temperature', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->decimal('height', 5, 2);
            $table->integer('pulse_rate');
            $table->integer('blood_sugar');
            $table->integer('resp');
            $table->integer('bp_systolic');
            $table->integer('bp_diastolic');
            $table->string('bp_status'); // Normal, High, Low
            $table->time('bp_time');
            $table->string('distance_aided');
            $table->string('distance_unaided');
            $table->string('distance_pinhole');
            $table->string('near_aided');
            $table->string('near_unaided');
            $table->decimal('iop_right', 5, 2); // Intraocular pressure (right eye)
            $table->decimal('iop_left', 5, 2);  // Intraocular pressure (left eye)
            $table->string('nurse_name')->nullable();
            $table->string('nurse_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('triages');
    }
};
