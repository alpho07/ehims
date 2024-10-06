<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationUnit extends Model
{
    use HasFactory;
    protected $table = 'organisation_units';

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($organisationUnit) {
            if ($organisationUnit->parent_id) {
                $parent = self::find($organisationUnit->parent_id);
                $organisationUnit->path = $parent->path . '/' . $organisationUnit->uid;
                $organisationUnit->hierarchy_level = $parent->hierarchy_level + 1;
            } else {
                // If it's a top-level unit like Kenya
                $organisationUnit->path = '/' . $organisationUnit->uid;
                $organisationUnit->hierarchy_level = 1;
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(OrganisationUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganisationUnit::class, 'parent_id');
    }
}
