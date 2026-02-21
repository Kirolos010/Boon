<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity_kg',
        'cost_per_kg',
        'total_cost',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:3',
        'cost_per_kg' => 'decimal:3',
        'total_cost' => 'decimal:3',
    ];

    /**
     * Get purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
