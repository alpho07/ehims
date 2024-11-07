<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FacilityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function view(User $user, Facility $facility)
    {
        // Super admin can view all facilities
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admins can view all facilities
        if ($user->hasRole('admin')) {
            return true;
        }

        // Hub admins can view their hub and spokes
        if ($user->hasRole('hub_admin') && $user->facility_id) {
            $hubFacility = $user->facility;

            // If the facility is the user's hub or one of its spokes
            return $facility->id === $hubFacility->id || $facility->parent_id === $hubFacility->id;
        }

        // Spoke admins can view only their spoke
        if ($user->hasRole('spoke_admin') && $user->facility_id) {
            return $facility->id === $user->facility_id;
        }

        return false;
    }
}
