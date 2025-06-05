<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['brand_id', 'brand_name','import_id', 'slug','meta_title','meta_description','keyword','status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
