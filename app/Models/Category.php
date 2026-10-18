<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // These are the columns we allow to be filled
    protected $fillable = [
        'name_en',
        'name_fa',
        'slug',
    ];

    // A Category has many Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}