<?php

namespace App\Models;

use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends BaseModel
{
    use HasFactory;

    protected $fillable = ['visit_id', 'clinic_id', 'status', 'rejection_reason'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    // Retrieve prescription data from the associated consultation
    public function getPrescriptionData()
    {
        return $this->visit->consultation->form_data['prescription'] ?? [];
    }

    // app/Models/Prescription.php

    public function prescriptionOrder()
    {
        return $this->hasOne(PrescriptionOrder::class);
    }


    public function order()
    {
        return $this->hasOne(PrescriptionOrder::class);
    }
}
