<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'sku',
        'main_category_id',
        'sub_category_id',
        'purchase_price_per_kg',
        'selling_price_per_kg',
        'current_stock_kg',
        'minimum_stock_alert',
        'supplier_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_price_per_kg' => 'decimal:3',
        'selling_price_per_kg' => 'decimal:3',
        'current_stock_kg' => 'decimal:3',
        'minimum_stock_alert' => 'decimal:3',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get main category
     */
    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }

    /**
     * Get sub category
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get user who created this product
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get invoice items for this product
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get purchase items for this product
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Get stock movements for this product
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock()
    {
        return $this->current_stock_kg <= $this->minimum_stock_alert;
    }

    /**
     * Get current stock in kilograms
     */
    public function currentStock()
    {
        return $this->current_stock_kg;
    }

    /**
     * Get selling price (alias for selling_price_per_kg)
     */
    public function getSellingPriceAttribute()
    {
        return $this->selling_price_per_kg;
    }

    /**
     * Get purchase price (alias for purchase_price_per_kg)
     */
    public function getPurchasePriceAttribute()
    {
        return $this->purchase_price_per_kg;
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginPercentage()
    {
        if ($this->purchase_price_per_kg == 0) return 0;
        $profit = $this->selling_price_per_kg - $this->purchase_price_per_kg;
        return ($profit / $this->purchase_price_per_kg) * 100;
    }

    /**
     * Filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('main_category_id', $categoryId);
    }

    /**
     * Filter by sub category
     */
    public function scopeBySubCategory($query, $subCategoryId)
    {
        return $query->where('sub_category_id', $subCategoryId);
    }

    /**
     * Filter by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Get low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock_kg', '<=', 'minimum_stock_alert');
    }

    /**
     * Search products by name or SKU
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('name_ar', 'like', "%{$term}%")
                     ->orWhere('sku', 'like', "%{$term}%");
    }
}
