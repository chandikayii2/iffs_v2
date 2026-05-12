<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'grn_number',
        'grn_date',
        'reference',
        'status',
        'created_by',
        'updated_by',
    ];

    public function grnProducts()
    {
        return $this->hasMany(GrnProduct::class);
    }
    
}
