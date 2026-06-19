<?php
// app/Models/TireIssueNoteItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TireIssueNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tire_issue_note_id', 'tire_id', 'vehicle_id', 'consumed_mileage', 'remark'
    ];

    public function tireIssueNote()
    {
        return $this->belongsTo(TireIssueNote::class);
    }

    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}