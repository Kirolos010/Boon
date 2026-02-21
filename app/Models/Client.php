<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'phone',
        'phone_2',
        'address',
        'address_ar',
        'credit_limit',
        'total_debt',
        'notes',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:3',
        'total_debt' => 'decimal:3',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get invoices for this client
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Check if client has available credit
     */
    public function hasAvailableCredit($amount)
    {
        $availableCredit = $this->credit_limit - $this->total_debt;
        return $availableCredit >= $amount;
    }

    /**
     * Get available credit amount
     */
    public function getAvailableCredit()
    {
        return $this->credit_limit - $this->total_debt;
    }

    /**
     * Get total purchases
     */
    public function getTotalPurchases()
    {
        return $this->invoices()->sum('total');
    }

    /**
     * Filter active clients
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Search clients by name or phone
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('name_ar', 'like', "%{$term}%")
                     ->orWhere('phone', 'like', "%{$term}%");
    }
}
