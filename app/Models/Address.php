<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'phone', 'first_name', 'last_name', 'phone_number', 
        'province', 'city', 'address', 'postal_code', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}