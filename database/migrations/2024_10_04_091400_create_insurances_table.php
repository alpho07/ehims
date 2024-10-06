<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsurancesTable extends Migration
{
    public function up()
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Name of the insurance provider
            $table->text('description')->nullable(); // Description about the insurance provider
            $table->boolean('is_active')->default(true); // Whether the insurance is currently active
            $table->timestamps(); // Created at & Updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('insurances');
    }
}
