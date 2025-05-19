<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;
    protected $fillable = ['merchant_id', 'merchant_name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
