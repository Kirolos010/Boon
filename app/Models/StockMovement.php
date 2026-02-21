<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'type',
        'quantity_kg',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:3',
    ];

    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    /**
     * Get product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get user who created this movement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Filter by product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Filter stock in
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    /**
     * Filter stock out
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    /**
     * Filter by reference type
     */
    public function scopeByReferenceType($query, $refType)
    {
        return $query->where('reference_type', $refType);
    }

    /**
     * Filter by reference id
     */
    public function scopeByReference($query, $refType, $refId)
    {
        return $query->where('reference_type', $refType)
                     ->where('reference_id', $refId);
    }
}
