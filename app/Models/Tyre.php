<?php
// app/Models/Tyre.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tyre extends Model
{
    use HasFactory;

    protected $table = 'tires';

    protected $fillable = [
        'serial_number', 'brand', 'size', 'type', 'status',
        'refill_count', 'max_refills', 'current_location',
        'purchase_date', 'purchase_price', 'notes',
        'vendor_id', 'consumption_mileage',
        'total_refill_count', 'rounds_finished', 'round_kms', 'tire_type', 'is_refilled'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'refill_count' => 'integer',
        'max_refills' => 'integer',
        'consumption_mileage' => 'integer',
        'total_refill_count' => 'integer',
        'rounds_finished' => 'integer',
        'round_kms' => 'array',
        'is_refilled' => 'boolean'
    ];

    // app/Models/Tyre.php - Add this relationship if not exists

public function allocations()
{
    return $this->hasMany(TyreAllocation::class, 'tire_id');
}

    public function currentAllocation()
    {
        return $this->hasOne(TyreAllocation::class, 'tire_id')->whereNull('removal_date');
    }

    public function refillingOrders()
    {
        return $this->belongsToMany(RefillingOrder::class, 'refilling_order_items', 'tire_id', 'refilling_order_id');
    }

    public function scrapRecord()
    {
        return $this->hasOne(TyreScrapRecord::class, 'tire_id');
    }
    
    public function vendor()
    {
        return $this->belongsTo(RefillingVendor::class, 'vendor_id');
    }

    public function canRefill()
    {
        return $this->refill_count < $this->max_refills && $this->status !== 'scrap';
    }
    
    // Calculate consumed mileage
    public function getConsumedMileageAttribute()
    {
        return $this->consumption_mileage ?? 0;
    }
    
}