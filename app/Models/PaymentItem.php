<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class PaymentItem extends BaseModel
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
