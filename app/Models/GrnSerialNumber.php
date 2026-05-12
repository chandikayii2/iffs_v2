<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnSerialNumber extends Model
{
    protected $fillable = [
        'grn_id',
        'grn_product_id',
        'product_id',
        'serial_number',
        'is_active',
        'created_by',
        'updated_by'
    ];
}
