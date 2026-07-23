<?php
// app/Models/TyreAllocation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TyreAllocation extends Model
{
    use HasFactory;

    protected $table = 'tire_allocations';

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

    public function tyre()
    {
        return $this->belongsTo(Tyre::class, 'tire_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}