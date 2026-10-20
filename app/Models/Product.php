<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_fa',
        'description_en',
        'description_fa',
        'weight',
        'purity',
        'metal_type',
        'making_charges',
        'stock',               // <-- ADDED
        'accessories_price',   // <-- ADDED
        'category_id',
        'collection_id',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'weight' => 'decimal:2',
        'making_charges' => 'integer',
        'stock' => 'integer',
        'accessories_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
    
    // Helper method to check if product is in stock
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}