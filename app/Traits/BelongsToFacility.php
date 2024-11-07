<?php

namespace App\Traits;

use App\Scopes\FacilityScope;

trait BelongsToFacility
{
    protected static function bootBelongsToFacility()
    {

        //static::addGlobalScope(new FacilityScope);
        if (static::class !== \App\Models\Facility::class) {
            static::addGlobalScope(new FacilityScope);
        }
    }
}
