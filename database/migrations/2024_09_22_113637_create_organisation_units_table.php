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
        Schema::create('organisation_units', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();  // Unique identifier for the unit
            $table->string('code')->nullable();  // Optional code
            $table->string('name');  // Unit name
            $table->foreignId('parent_id')->nullable()->constrained('organisation_units')->onDelete('cascade');  // Parent unit reference
            $table->string('path');  // Hierarchy path (e.g., /Kenya/County/Subcounty/Ward/Facility)
            $table->integer('hierarchy_level');  // Level in the hierarchy (1: Kenya, 2: County, etc.)
            $table->timestamps();  // Timestamps for created and updated records
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_units');
    }
};
