<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'type',
        'client_id',
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'amount_paid',
        'remaining_balance',
        'status',
        'invoice_date',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:3',
        'discount' => 'decimal:3',
        'tax' => 'decimal:3',
        'total' => 'decimal:3',
        'amount_paid' => 'decimal:3',
        'remaining_balance' => 'decimal:3',
        'invoice_date' => 'date',
    ];

    protected $dates = ['deleted_at'];

    const TYPE_REGULAR = 'regular';
    const TYPE_QUICK = 'quick';
    const STATUS_PAID = 'paid';
    const STATUS_PARTIAL = 'partial';
    const STATUS_UNPAID = 'unpaid';

    /**
     * Get client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get user who created this invoice
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get invoice items
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get invoice payments
     */
    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if invoice is partial
     */
    public function isPartial()
    {
        return $this->status === self::STATUS_PARTIAL;
    }

    /**
     * Check if invoice is unpaid
     */
    public function isUnpaid()
    {
        return $this->status === self::STATUS_UNPAID;
    }

    /**
     * Calculate profit from this invoice
     */
    public function calculateProfit()
    {
        $profit = 0;
        foreach ($this->items as $item) {
            $costPrice = $item->product->purchase_price_per_kg;
            $sellingPrice = $item->unit_price;
            $profit += ($sellingPrice - $costPrice) * $item->quantity_kg;
        }
        return $profit;
    }

    /**
     * Filter quick sales
     */
    public function scopeQuickSales($query)
    {
        return $query->where('type', self::TYPE_QUICK);
    }

    /**
     * Filter regular invoices
     */
    public function scopeRegular($query)
    {
        return $query->where('type', self::TYPE_REGULAR);
    }

    /**
     * Filter by client
     */
    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Filter by date range
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    /**
     * Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for today's invoices
     */
    public function scopeToday($query)
    {
        return $query->whereDate('invoice_date', today());
    }

    /**
     * Scope for this month's invoices
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('invoice_date', now()->month)
                     ->whereYear('invoice_date', now()->year);
    }
}
