<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
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
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
