<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;
    protected $fillable = ['image_url', 'merchant_id', 'merchant_name', 'import_id', 'slug','meta_title','meta_description','keyword','status'];

    public function products()
    {
        return $this->hasMany(Product::class, 'merchant_id', 'id');
    }


    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'merchant_id', 'merchant_id');
    }


}
