<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
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
}
