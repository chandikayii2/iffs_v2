<?php
// app/Models/TyreScrapRecord.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TyreScrapRecord extends Model
{
    use HasFactory;

    protected $table = 'tire_scrap_records';

    protected $fillable = [
        'tire_id', 'scrap_date', 'scrap_reason', 'final_mileage',
        'disposal_method', 'notes',
        'scrap_category', 'sale_price', 'buyer_name', 'sale_payment_method',
        'sale_payment_status', 'sale_reference', 'sale_date'
    ];

    protected $casts = [
        'scrap_date' => 'date',
        'final_mileage' => 'integer',
        'sale_price' => 'decimal:2',
        'sale_date' => 'date'
    ];

    public function tyre()
    {
        return $this->belongsTo(Tyre::class, 'tire_id');
    }
}