<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'main_category_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
    ];

    /**
     * Get parent main category
     */
    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }

    /**
     * Get products in this sub category
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
