<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionOrder extends BaseModel
{
    use HasFactory;

    protected $fillable = ['prescription_id', 'visit_id', 'status'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionOrderItem::class);
    }

    public function updateStatus()
    {
        // Update logic if needed, based on order items or approval
        if ($this->items()->where('status', 'pending')->exists()) {
            $this->status = 'pending';
        } else {
            $this->status = 'dispensed';
        }
        $this->save();
    }
}
