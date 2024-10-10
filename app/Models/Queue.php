<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'visit_id',
        'patient_id',
        'position',
        'status',
        'referred_from_id',
    ];

    /**
     * Get the clinic associated with the queue.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the visit associated with the queue.
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the patient in this queue.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the clinic from where the patient was referred.
     */
    public function referredFrom()
    {
        return $this->belongsTo(Clinic::class, 'referred_from_id');
    }
}
