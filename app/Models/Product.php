<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'aw_product_id',
        'product_name',
        'aw_deep_link',
        'merchant_product_id',
        'merchant_image_url',
        'description',
        'merchant_category',
        'search_price',
        'merchant_id',
        'category_id',
        'brand_id',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
