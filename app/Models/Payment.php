<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'total_amount',
        'is_paid',
        'visit_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
