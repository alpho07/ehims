<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryProduct  extends Model
{
    //protected $table = 'inventory_products';

    protected $fillable = [
        'item',
        'description',
        'system_code',
        'type',
        'gender',
        'price',
    ];
}
