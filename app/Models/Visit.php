<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    // Automatically load relationships when querying the visit model
    protected $with = ['patient', 'triage'];

    // Fillable fields for mass assignment
    protected $fillable = [
        'patient_id',
        'clinic_id',
        'visit_start_time',
        'visit_end_time',
        'status',
        'referred_from_id',
        'referred_to_id',
        'previous_clinics',
        'staff_seen',
    ];

    // Cast JSON fields to array for easy manipulation
    protected $casts = [
        'previous_clinics' => 'array',
        'staff_seen' => 'array',
    ];

    /**
     * Define the relationship between Visit and Patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Define the relationship between Visit and Triage.
     */
    public function triage()
    {
        return $this->hasOne(Triage::class);
    }

    /**
     * Define the relationship between Visit and Consultation.
     */
    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    /**
     * Calculate the visit duration.
     */
    public function getVisitDurationAttribute()
    {
        return $this->visit_end_time
            ? $this->visit_end_time->diffForHumans($this->visit_start_time)
            : null;
    }

    /**
     * Get the clinic from which the patient was referred.
     */
    public function referredFrom()
    {
        return $this->belongsTo(Clinic::class, 'referred_from_id');
    }

    /**
     * Get the clinic to which the patient was referred.
     */
    public function referredTo()
    {
        return $this->belongsTo(Clinic::class, 'referred_to_id');
    }

    /**
     * Define the relationship between Visit and Referrals.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Define the relationship between Visit and Queue.
     */
    public function queues()
    {
        return $this->hasMany(Queue::class);
    }


    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');  // Make sure the foreign key is 'clinic_id'
    }


    /**
     * Refer the patient to another clinic.
     *
     * @param int $nextClinicId
     * @return void
     */
    public function referToClinic($nextClinicId)
    {
        // Get the next clinic
        $nextClinic = Clinic::findOrFail($nextClinicId);

        // Update visit's previous_clinics and staff_seen
        $this->previous_clinics = array_merge($this->previous_clinics ?? [], [$this->clinic_id]);
        $this->staff_seen = array_merge($this->staff_seen ?? [], [auth()->user()->id]);
        $this->clinic_id = $nextClinicId;
        $this->status = $nextClinic->name; // e.g., 'Low Vision Clinic'

        $this->save();

        // Update queue status of current clinic to 'referred'
        Queue::where('visit_id', $this->id)
            ->where('clinic_id', $this->referred_from_id ?? $this->clinic_id)
            ->update(['status' => 'referred']);

        // Add to the new clinic's queue
        $lastQueuePosition = Queue::where('clinic_id', $nextClinicId)->max('position');
        $newPosition = $lastQueuePosition ? $lastQueuePosition + 1 : 1;

        Queue::create([
            'clinic_id' => $nextClinicId,
            'visit_id' => $this->id,
            'patient_id' => $this->patient_id,
            'position' => $newPosition,
            'status' => 'waiting',
            'referred_from_id' => $this->referred_from_id ?? $this->clinic_id,
        ]);
    }
}
