<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToFacility;
use App\Traits\AssignsFacility;

class BaseModel extends Model
{
    use BelongsToFacility, AssignsFacility;

    // You can also add common functionality here, like timestamps or soft deletes, etc.

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
