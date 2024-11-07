<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AssignsFacility
{
    protected static function bootAssignsFacility()
    {
        static::creating(function ($model) {
            $user = Auth::user();

            if ($user && !$model->facility_id) {
                $model->facility_id = $user->facility_id;
            }
        });

        static::updating(function ($model) {
            // Prevent changing facility_id on update
            if ($model->isDirty('facility_id')) {
                $model->facility_id = $model->getOriginal('facility_id');
            }
        });
    }
}
