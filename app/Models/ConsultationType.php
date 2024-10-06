<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Get the consultation fees associated with this consultation type.
     */
    public function consultationFees()
    {
        return $this->hasMany(ConsultationFee::class);
    }
}
