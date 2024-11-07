<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class StoreUserData
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Store necessary user data in session
            session([
                'user_roles' => $user->getRoleNames()->toArray(),
                'user_facility_id' => $user->facility_id,
                'facility'=>$user->facility->toArray()
            ]);
        }

        return $next($request);
    }
}
