<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['clinic_id', 'service_type_id'];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
