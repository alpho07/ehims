<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'referred_from_id',
        'referred_to_id',
        'reason',
    ];

    /**
     * Get the visit that this referral is associated with.
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the clinic the patient was referred from.
     */
    public function referredFrom()
    {
        return $this->belongsTo(Clinic::class, 'referred_from_id');
    }

    /**
     * Get the clinic the patient was referred to.
     */
    public function referredTo()
    {
        return $this->belongsTo(Clinic::class, 'referred_to_id');
    }
}
