<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;


    protected $fillable = [
        'visit_id',
        'triage_id',
        'doctor_id',
        'doctors_comments',
        'prescription',
        // Right Eye Prescription Fields
        'right_eye_distance_sphere',
        'right_eye_distance_cylinder',
        'right_eye_distance_axis',
        'right_eye_reading_sphere',
        'right_eye_reading_cylinder',
        'right_eye_reading_axis',
        // Left Eye Prescription Fields
        'left_eye_distance_sphere',
        'left_eye_distance_cylinder',
        'left_eye_distance_axis',
        'left_eye_reading_sphere',
        'left_eye_reading_cylinder',
        'left_eye_reading_axis',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function triage()
    {
        return $this->belongsTo(Triage::class);
    }
}
