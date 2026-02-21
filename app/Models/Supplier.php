<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'phone',
        'email',
        'address',
        'address_ar',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get products from this supplier
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get purchases from this supplier
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
