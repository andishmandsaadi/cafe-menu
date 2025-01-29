<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeOwner extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'username', 'password'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'cafe_owner_category');
    }
}
