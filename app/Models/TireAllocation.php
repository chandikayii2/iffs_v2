<?php
// app/Models/TireAllocation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TireAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tire_id', 'vehicle_id', 'mileage_at_installation', 'mileage_at_removal',
        'position', 'installation_date', 'removal_date', 'removal_reason',
        'remark', 'consumed_mileage' // Added remark and consumed_mileage
    ];

    protected $casts = [
        'installation_date' => 'date',
        'removal_date' => 'date',
        'mileage_at_installation' => 'integer',
        'mileage_at_removal' => 'integer',
        'consumed_mileage' => 'integer' // Added
    ];

    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}