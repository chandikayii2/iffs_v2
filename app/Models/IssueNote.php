<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNote extends Model
{
    protected $fillable = [
        'issue_note_number',
        'issue_note_date',
        'driver_name',
        'lorry_number',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    public function issueNoteProducts()
    {
        return $this->hasMany(IssueNoteProduct::class);
    }
}
