<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BPReading extends Model
{
    protected $fillable = [
        'triage_id',
        'systolic',
        'diastolic',
        'status',
        'time',
    ];

    public function triage()
    {
        return $this->belongsTo(Triage::class);
    }
}
