<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GlobalFacilityPolicy
{
    /**
     * Determine if the user can view any model.
     */
    public function view(User $user, Model $model): bool
    {
        // Super admin and admin can access all records
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Check if the user belongs to the same facility as the model
        if ($user->hasRole('hub_admin')) {
            $facilityIds = $user->facility->spokes->pluck('id')->toArray();
            $facilityIds[] = $user->facility_id; // Include hub itself
            return in_array($model->facility_id, $facilityIds);
        }

        if ($user->hasRole('spoke_admin')) {
            return $user->facility_id === $model->facility_id;
        }

        // Default: deny access
        return false;
    }

    // You can extend this for other actions such as create, update, delete, etc.
}
