<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $with=['patient','triage'];

    protected $fillable = ['patient_id', 'visit_start_time', 'visit_end_time', 'status'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function triage()
    {
        return $this->hasOne(Triage::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function getVisitDurationAttribute()
    {
        return $this->visit_end_time
            ? $this->visit_end_time->diffForHumans($this->visit_start_time)
            : null;
    }
}
