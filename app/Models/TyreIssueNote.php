<?php
// app/Models/TyreIssueNote.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TyreIssueNote extends Model
{
    use HasFactory;

    protected $table = 'tire_issue_notes';

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
        return $this->hasMany(TyreIssueNoteItem::class, 'tire_issue_note_id');
    }

    public function tyres()
    {
        return $this->belongsToMany(Tyre::class, 'tire_issue_note_items', 'tire_issue_note_id', 'tire_id')
                    ->withPivot('consumed_mileage', 'remark')
                    ->withTimestamps();
    }
}