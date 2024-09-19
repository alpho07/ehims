<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eyewear extends Model
{
    protected $table = 'eyewear_inventory';

    protected $fillable = [
        'name',
        'prescription',
        'pupillary_distance',
        'lens_type',
        'lens_material',
        'lens_coating',
        'frame_style',
        'frame_material',
        'frame_color',
        'bridge_size',
        'temple_length',
        'lens_width',
        'lens_height',
        'gender',
        'brand',
        'frame_size',
        'weight',
        'uv_protection',
        'stock_quantity',
        'reorder_level',
        'reorder_quantity',
        'storage_conditions',
        'price',
    ];
}
