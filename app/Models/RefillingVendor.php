<?php
// app/Models/RefillingVendor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefillingVendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact_person', 'phone', 'email', 'address'
    ];

    public function refillingOrders()
    {
        // Specify the correct foreign key column name
        return $this->hasMany(RefillingOrder::class, 'vendor_id');
    }
}