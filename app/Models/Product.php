<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
    ];

    // Many-to-many relationship with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function cafeOwners()
    {
        return $this->belongsToMany(CafeOwner::class, 'cafe_owner_product')
                    ->withPivot('category_id', 'price')
                    ->withTimestamps();
    }
}