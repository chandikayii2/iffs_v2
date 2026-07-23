<?php
// app/Models/TyreIssueNoteItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TyreIssueNoteItem extends Model
{
    use HasFactory;

    protected $table = 'tire_issue_note_items';

    protected $fillable = [
        'tire_issue_note_id', 'tire_id', 'vehicle_id', 'consumed_mileage', 'remark'
    ];

    public function tyreIssueNote()
    {
        return $this->belongsTo(TyreIssueNote::class, 'tire_issue_note_id');
    }

    public function tyre()
    {
        return $this->belongsTo(Tyre::class, 'tire_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}