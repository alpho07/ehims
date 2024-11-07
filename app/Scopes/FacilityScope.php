<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class FacilityScope implements Scope
{
    protected string $column = '';

    public function apply(Builder $builder, Model $model)
    {
        // Skip applying scope to User model to avoid recursion
        if ($model instanceof \App\Models\User) {
            return;
        }

        // Determine the correct column for the model: 'id' for Facility, 'facility_id' for other models
        if ($model instanceof \App\Models\Facility) {
            $this->column = 'id'; // For Facility model
        } else {
            $this->column = 'facility_id'; // For other models
        }

        // Retrieve the authenticated user's roles and facility ID from the session
        $roles = session('user_roles', []);
        $facilityId = session('user_facility_id');

        // If the user has no roles or no assigned facility, restrict access to no records
        if (empty($roles) || !$facilityId) {
            $builder->whereNull($this->column);
            return;
        }

        // Super Admin or Admin have full access
        if (in_array('super_admin', $roles) || in_array('admin', $roles)) {
            return; // No restrictions applied
        }

        // Hub Admin: can see hub and all spokes under their hub
        if (in_array('hub_admin', $roles)) {
            $facilityIds = DB::table('facilities')
                ->where('parent_id', $facilityId)
                ->orWhere('id', $facilityId)
                ->pluck('id')
                ->toArray();

            $builder->whereIn($this->column, $facilityIds);
            return;
        }

        // Spoke Admin: can only see their spoke
        if (in_array('spoke_admin', $roles)) {
            $builder->where($this->column, $facilityId);
            return;
        }

        // Doctors, Nurses, Pharmacists, and Receptionist (non-admin roles) can access data in their assigned hub or spoke
        if (in_array('Doctor', $roles) || in_array('Nurse', $roles) || in_array('Pharmacy', $roles) || in_array('Receptionist', $roles)) {
            $builder->where($this->column, $facilityId);
            return;
        }

        // For other cases or unknown roles, restrict access
        $builder->whereNull($this->column);
    }
}
