<?php

namespace App\Models;

use App\Traits\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use BelongsToFacility;

    protected $fillable = [
        'facility_name',
        'mfl_code',
        'county',
        'subcounty',
        'ward',
        'facility_type',
        'parent_id',
    ];

    // Define relationship to parent facility (hub)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'parent_id');
    }

    // Define relationship to child facilities (spokes)
    public function children(): HasMany
    {
        return $this->hasMany(Facility::class, 'parent_id');
    }

    // Define relationship to users
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
