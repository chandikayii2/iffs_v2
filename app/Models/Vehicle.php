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

    public function tireAllocations()
    {
        return $this->hasMany(TireAllocation::class);
    }

    public function currentTires()
    {
        return $this->belongsToMany(Tire::class, 'tire_allocations')
                    ->whereNull('tire_allocations.removal_date')
                    ->withPivot('mileage_at_installation', 'position');
    }
    public function tireIssueNotes()
{
    return $this->hasMany(TireIssueNote::class);
}
}