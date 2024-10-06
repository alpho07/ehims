<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_type_id',
        'fee_amount',
        'is_active',
    ];

    /**
     * Get the consultation type associated with the fee.
     */
    public function consultationType()
    {
        return $this->belongsTo(ConsultationType::class);
    }
}
