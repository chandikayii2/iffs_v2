<?php
// app/Models/TireScrapRecord.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TireScrapRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'tire_id', 'scrap_date', 'scrap_reason', 'final_mileage',
        'disposal_method', 'notes'
    ];

    protected $casts = [
        'scrap_date' => 'date',
        'final_mileage' => 'integer'
    ];

    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }
}