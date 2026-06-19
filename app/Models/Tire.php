<?php
// app/Models/Tire.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tire extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number', 'brand', 'size', 'type', 'status',
        'refill_count', 'max_refills', 'current_location',
        'purchase_date', 'purchase_price', 'notes',
        'vendor_id', 'consumption_mileage' // Added vendor_id and consumption_mileage
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'refill_count' => 'integer',
        'max_refills' => 'integer',
        'consumption_mileage' => 'integer' // Added
    ];

    // app/Models/Tire.php - Add this relationship if not exists

public function allocations()
{
    return $this->hasMany(TireAllocation::class);
}

    public function currentAllocation()
    {
        return $this->hasOne(TireAllocation::class)->whereNull('removal_date');
    }

    public function refillingOrders()
    {
        return $this->belongsToMany(RefillingOrder::class, 'refilling_order_items');
    }

    public function scrapRecord()
    {
        return $this->hasOne(TireScrapRecord::class);
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