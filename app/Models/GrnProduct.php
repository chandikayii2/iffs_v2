<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnProduct extends Model
{
    protected $fillable = [
        'grn_id',
        'purchase_order_product_id',
        'product_id',
        'received_quantity',
        'out_quantity',
        'issued_status',
        'received_price',
    ];
        // Define the inverse relationship to Grn
        public function grn()
        {
            return $this->belongsTo(Grn::class);
        }
    public function grnSerialNumbers()
    {
        return $this->hasMany(GrnSerialNumber::class, 'grn_product_id', 'id');
    }


}
