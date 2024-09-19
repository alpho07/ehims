<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $table = 'drug_inventory';

    protected $fillable = [
        'drug_name', 'drug_category', 'dosage_form', 'dosage_strength',
        'manufacturer', 'batch_number', 'expiry_date', 'quantity_in_stock',
        'reorder_level', 'reorder_quantity', 'storage_conditions', 'price',
    ];
}

