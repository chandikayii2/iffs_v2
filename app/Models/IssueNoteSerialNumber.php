<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNoteSerialNumber extends Model
{
    protected $fillable = [
        'issue_note_id',
        'issue_note_product_id',
        'product_id',
        'serial_number_id',
        'serial_number',
        'created_by',
        'updated_by'
    ];
}
