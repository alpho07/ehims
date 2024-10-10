<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id', 'clinic_id', 'form_data', 'referred_to_id','triage_id',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function referredTo()
    {
        return $this->belongsTo(Clinic::class, 'referred_to_id');
    }
}
