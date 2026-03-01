<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity_kg',
        'unit_price',
        'total',
        'cost_price_per_kg',
        'item_profit',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:3',
        'unit_price' => 'decimal:3',
        'total' => 'decimal:3',
        'cost_price_per_kg' => 'decimal:3',
        'item_profit' => 'decimal:3',
    ];

    /**
     * Get invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate profit for this item
     */
    public function calculateProfit()
    {
        $costPrice = $this->product->purchase_price_per_kg;
        $sellingPrice = $this->unit_price;
        return ($sellingPrice - $costPrice) * $this->quantity_kg;
    }
}
