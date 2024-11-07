<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBPReadingsTable extends Migration
{
    public function up()
    {
        Schema::create('b_p_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triage_id')->constrained()->onDelete('cascade');
            $table->integer('systolic');
            $table->integer('diastolic');
            $table->string('status')->nullable(); // e.g., 'normal', 'high', 'low'
            $table->time('time');
            $table->integer('facility_id')->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('b_p_readings');
    }
}
