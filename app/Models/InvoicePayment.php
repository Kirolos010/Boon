<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'payment_date' => 'date',
    ];

    const METHOD_CASH = 'cash';
    const METHOD_CHECK = 'check';
    const METHOD_TRANSFER = 'transfer';
    const METHOD_OTHER = 'other';

    /**
     * Get invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get user who recorded this payment
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Filter by invoice
     */
    public function scopeByInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    /**
     * Filter by payment method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Filter by date range
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }
}
