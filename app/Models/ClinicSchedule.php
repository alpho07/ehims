<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'day',
        'start_time',
        'end_time',
    ];

    /**
     * Get the clinic associated with this schedule.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
