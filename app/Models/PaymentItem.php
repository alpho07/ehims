<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'description',
    ];

    /**
     * Relationship with PaymentDetail.
     * Each PaymentItem can be associated with many PaymentDetails,
     * since a payment item can be part of many payment transactions.
     */
    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class);
    }
}
