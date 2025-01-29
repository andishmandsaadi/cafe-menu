<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image'];

    public function cafeOwners()
    {
        return $this->belongsToMany(CafeOwner::class, 'cafe_owner_category');
    }

    // Many-to-many relationship with products
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
