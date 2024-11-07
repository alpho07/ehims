<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'file_number',
        'hospital_number',
        'email',
        'address',
        'name',
        'dob',
        'gender',
        'phone',
        'source',
        'referral_facility',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


  
}
