<?php
// app/Models/TireIssueNote.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TireIssueNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_note_number', 'issue_date', 'vehicle_id', 'remarks', 'status'
    ];

    protected $casts = [
        'issue_date' => 'date'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items()
    {
        return $this->hasMany(TireIssueNoteItem::class);
    }

    public function tires()
    {
        return $this->belongsToMany(Tire::class, 'tire_issue_note_items')
                    ->withPivot('consumed_mileage', 'remark')
                    ->withTimestamps();
    }
}