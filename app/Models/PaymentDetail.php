<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentDetail extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'payment_item_id',
        'amount',
        'payment_type',
        'payment_mode',
        'payment_reference',
        'insurance_id',
        'is_copay',
        'waiver_amount',
        'total_amount',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }

    public function insurance()
    {
        return $this->hasOne(Insurance::class, 'id','insurance_id');
    }
}
