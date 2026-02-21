<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'user_id',
        'subtotal',
        'tax',
        'total_cost',
        'purchase_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:3',
        'tax' => 'decimal:3',
        'total_cost' => 'decimal:3',
        'purchase_date' => 'date',
    ];

    protected $dates = ['deleted_at'];

    const STATUS_PENDING = 'pending';
    const STATUS_RECEIVED = 'received';
    const STATUS_PARTIAL = 'partial';

    /**
     * Get supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get user who created this purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get purchase items
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Check if purchase is received
     */
    public function isReceived()
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    /**
     * Filter by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Filter by date range
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('purchase_date', [$startDate, $endDate]);
    }

    /**
     * Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending purchases
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
