<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationTypesTable extends Migration
{
    public function up()
    {
        Schema::create('consultation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Consultation type name, e.g., General Consultation, Specialist Consultation
            $table->text('description')->nullable(); // Description of the consultation type
            $table->boolean('is_active')->default(true); // Whether the consultation type is active
            $table->timestamps(); // Created at & Updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultation_types');
    }
}
