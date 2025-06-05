<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'merchant_id',
        'parent_id',
        'name',
        'import_id',
        'slug',
        'meta_title',
        'keyword',
        'status'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    // In Subcategory model
    public function parent()
    {
        return $this->belongsTo(Subcategory::class, 'parent_id');  // Belongs to a parent subcategory
    }

    public function children()
    {
        return $this->hasMany(Subcategory::class, 'parent_id');  // Has many child subcategories
    }
}
