<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'product_name',
        'description',
        'stock_quantity',
        'unit_price',
        'unit_of_measurement',
        'image_file',
        'serial_number',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function serialNumbers()
    {
        return $this->hasMany(GrnSerialNumber::class, 'product_id');
    }
}
