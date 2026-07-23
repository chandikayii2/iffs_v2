<?php
// app/Models/Vehicle.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'lorry_number', 'driver_name', 'driver_contact', 'current_mileage', 'status'
    ];

    public function tyreAllocations()
    {
        return $this->hasMany(TyreAllocation::class, 'vehicle_id');
    }

    public function currentTyres()
    {
        return $this->belongsToMany(Tyre::class, 'tire_allocations', 'vehicle_id', 'tire_id')
                    ->whereNull('tire_allocations.removal_date')
                    ->withPivot('mileage_at_installation', 'position');
    }
    
    public function tyreIssueNotes()
    {
        return $this->hasMany(TyreIssueNote::class, 'vehicle_id');
    }
}