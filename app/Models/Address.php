<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'first_name',
        'last_name',
        'phone_number',
        'province',
        'city',
        'address',
        'postal_code',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}