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
        'image',
        // SEO FIELDS ADDED HERE
        'meta_title',
        'meta_description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}