<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_fa',
        'slug',
        'image', // Added this
    ];

    // A Collection has many Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}