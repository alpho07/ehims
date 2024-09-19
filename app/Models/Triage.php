<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Triage extends Model
{
    use HasFactory;

    protected $with = ['consultation','bpReadings'];

    protected $fillable = [
        'visit_id',
        'date',
        'time',
        'age',
        'temperature',
        'weight',
        'height',
        'pulse',
        'resp',
        'bp_systolic',
        'bp_diastolic',
        'bp_status',
        'bp_time',
        'visual_acuity',
        'iop',
        'nurse_name',
        'nurse_signature'
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }


    public function bpReadings()
    {
        return $this->hasMany(BPReading::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }
}
