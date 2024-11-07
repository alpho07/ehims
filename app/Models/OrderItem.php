<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'inventory_product_id', 'available_stock', 'requested_stock'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function inventoryProduct()
    {
        return $this->belongsTo(InventoryProduct::class);
    }
}
