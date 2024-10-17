<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'day', 'description'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function schedules()
    {
        return $this->hasMany(ClinicSchedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }


    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
