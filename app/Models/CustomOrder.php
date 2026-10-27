<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jewelry_type',
        'metal_type',
        'reference_image',
        'details',
        'budget',
        // 'status' and 'admin_notes' REMOVED. 
        // Hackers can no longer fake an "approved" status!
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}