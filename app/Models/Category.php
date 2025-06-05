<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'category_name','import_id', 'slug','meta_title','meta_description','keyword','status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
