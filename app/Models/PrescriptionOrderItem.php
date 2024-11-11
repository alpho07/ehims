<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['prescription_order_id', 'inventory_product_id', 'available_stock', 'requested_stock'];

    public function prescriptionOrder()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function inventoryProduct()
    {
        return $this->belongsTo(HubFacilityInventory::class, 'inventory_product_id');
    }
}
