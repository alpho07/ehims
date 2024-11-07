<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('OrgUnitID')->unique();  // Unique identifier for the facility
            $table->string('mflCode')->unique();    // Unique identifier per MFL Code
            $table->string('facilityName');         // Facility name (Hub or Spoke)
            $table->enum('facilityType', ['hub', 'spoke']); // To identify whether it's a hub or spoke
            $table->string('Ward')->nullable();     // The ward where the facility is located
            $table->string('Subcounty')->nullable(); // Subcounty info
            $table->string('County')->nullable();    // County info
            $table->foreignId('parent_id')->nullable()->constrained('facilities')->onDelete('cascade');  // Parent Hub
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facilities');
    }
}
