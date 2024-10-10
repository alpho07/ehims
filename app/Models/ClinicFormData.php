<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicFormData extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'visit_id',
        'patient_id',
        'form_data',
    ];

    /**
     * Get the clinic associated with the form.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the visit associated with the form.
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the patient associated with the form.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get and decode the form data.
     */
    public function getFormDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Set the form data.
     */
    public function setFormDataAttribute($value)
    {
        $this->attributes['form_data'] = json_encode($value);
    }
}
