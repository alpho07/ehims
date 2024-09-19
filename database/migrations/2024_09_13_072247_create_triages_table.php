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
            $table->decimal('weight');
            $table->decimal('height');
            $table->integer('pulse');
            $table->integer('resp');
            $table->integer('bp_systolic');
            $table->integer('bp_diastolic');
            $table->string('bp_status'); // Normal, High, Low
            $table->time('bp_time');
            $table->string('visual_acuity');
            $table->decimal('iop', 5, 2); // Intraocular pressure
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
