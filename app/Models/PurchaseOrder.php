<?php
// app/Models/PurchaseOrder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_number',
        'supplier_id',
        'purchase_order_date',
        'reference',
        'created_by',
        'updated_by',
        'status', // Add status column
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'purchase_order_date' => 'date',
    ];

    // Relationship with Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relationship with Purchase Order Products
    public function products()
    {
        return $this->hasMany(PurchaseOrderProduct::class, 'purchase_order_id');
    }

    // Relationship with GRN
    public function grns()
    {
        return $this->hasMany(Grn::class, 'purchase_order_id');
    }
}