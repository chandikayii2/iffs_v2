<?php
// app/Models/RefillingOrder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefillingOrder extends Model
{
    use HasFactory;

    protected $table = 'refilling_orders';

    protected $fillable = [
        'order_number', 'vendor_id', 'sent_date',
        'received_date', 'status', 'total_cost', 'notes',
        'payment_method', 'payment_reference', 'payment_status', 'paid_amount', 'payment_date', 'payment_notes'
    ];

    protected $casts = [
        'sent_date' => 'date',
        'received_date' => 'date',
        'total_cost' => 'decimal:2',
        'payment_date' => 'date'
    ];

    public function vendor()
    {
        return $this->belongsTo(RefillingVendor::class, 'vendor_id');
    }

    public function tyres()
    {
        return $this->belongsToMany(Tyre::class, 'refilling_order_items', 'refilling_order_id', 'tire_id')
                    ->withPivot('refilling_cost', 'notes')
                    ->withTimestamps();
    }
}