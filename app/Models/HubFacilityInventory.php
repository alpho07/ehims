<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubFacilityInventory extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'facility_id',
        'available_quantity',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryProduct::class, 'item_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionOrderItem::class, 'inventory_product_id');
    }

    public function getQuantityAvailableAttribute()
    {
        $facilityIds = \DB::table('facilities')
            ->where('parent_id', $this->facility_id)
            ->orWhere('id', $this->facility_id)
            ->pluck('id');

        return HubFacilityInventory::whereIn('facility_id', $facilityIds)
            ->where('item_id', $this->item_id)
            ->sum('available_quantity');
    }


    public function getSpokeQuantitiesAttribute()
    {
        // Get spoke IDs for this hub (excluding the hub itself)
        $spokeIds = Facility::where('parent_id', $this->facility_id)->pluck('id');

        // Query spoke inventories for the same item
        return self::whereIn('facility_id', $spokeIds)
            ->where('item_id', $this->item_id)
            ->get();
    }



    public function spokeQuantities()
    {
        // Get IDs of facilities (spokes) where the hub's ID is the parent_id
        $spokeIds = Facility::where('parent_id', $this->facility_id)->pluck('id');

        // Return a filtered relation to only include spoke quantities
        $data = $this->hasMany(HubFacilityInventory::class, 'facility_id', 'facility_id')
            ->whereIn('facility_id', $spokeIds)
            ->where('item_id', $this->item_id);

        dd($data->toSql());
    }
}
