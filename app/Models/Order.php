<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'orderable_id',
        'orderable_type',
        'quantity',
        'status',
        'order_date',
        'delivery_date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function orderable()
    {
        return $this->morphTo();
    }
}
