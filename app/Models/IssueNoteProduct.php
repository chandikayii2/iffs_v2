<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNoteProduct extends Model
{
    protected $fillable = [
        'issue_note_id',
        'product_id',
        'issued_quantity',
    ];
        // Define the relationship to IssueNote
        public function issueNote()
        {
            return $this->belongsTo(IssueNote::class);
        }
    public function issueSerialNumbers()
    {
        return $this->hasMany(IssueNoteSerialNumber::class, 'issue_note_product_id', 'id');
    }
}
