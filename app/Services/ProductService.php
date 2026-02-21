<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class ProductService
{
    /**
     * Create a new product
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            try {
                // Generate SKU if not provided
                if (empty($data['sku'])) {
                    $data['sku'] = $this->generateSku($data['name_ar']);
                }

                // Validate SKU is unique
                if (Product::where('sku', $data['sku'])->exists()) {
                    throw new Exception('كود المنتج موجود بالفعل');
                }

                // Create product with name_ar as both name and name_ar
                $product = Product::create([
                    'name' => $data['name_ar'],  // Set name same as name_ar
                    'name_ar' => $data['name_ar'],
                    'sku' => $data['sku'],
                    'main_category_id' => $data['main_category_id'],
                    'sub_category_id' => $data['sub_category_id'],
                    'purchase_price_per_kg' => $data['purchase_price_per_kg'],
                    'selling_price_per_kg' => $data['selling_price_per_kg'],
                    'current_stock_kg' => $data['current_stock_kg'] ?? 0,
                    'minimum_stock_alert' => $data['minimum_stock_alert'] ?? 0,
                    'supplier_id' => $data['supplier_id'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);

                // Record initial stock movement if stock provided
                if (($data['current_stock_kg'] ?? 0) > 0) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => StockMovement::TYPE_IN,
                        'quantity_kg' => $data['current_stock_kg'],
                        'reference_type' => 'adjustment',
                        'notes' => 'رصيد افتتاحي',
                        'created_by' => Auth::id(),
                    ]);
                }

                return $product;
            } catch (Exception $e) {
                throw new Exception('خطأ في إنشاء المنتج: ' . $e->getMessage());
            }
        });
    }

    /**
     * Update product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            try {
                // Keep existing SKU if not provided
                $sku = $data['sku'] ?? $product->sku;

                // Check if SKU changed and is unique
                if ($sku !== $product->sku &&
                    Product::where('sku', $sku)->exists()) {
                    throw new Exception('كود المنتج موجود بالفعل');
                }

                $product->update([
                    'name' => $data['name_ar'] ?? $product->name_ar,  // Update name same as name_ar
                    'name_ar' => $data['name_ar'] ?? $product->name_ar,
                    'sku' => $sku,
                    'main_category_id' => $data['main_category_id'] ?? $product->main_category_id,
                    'sub_category_id' => $data['sub_category_id'] ?? $product->sub_category_id,
                    'purchase_price_per_kg' => $data['purchase_price_per_kg'] ?? $product->purchase_price_per_kg,
                    'selling_price_per_kg' => $data['selling_price_per_kg'] ?? $product->selling_price_per_kg,
                    'minimum_stock_alert' => $data['minimum_stock_alert'] ?? $product->minimum_stock_alert,
                    'supplier_id' => $data['supplier_id'] ?? $product->supplier_id,
                    'notes' => $data['notes'] ?? $product->notes,
                ]);

                // Handle stock adjustment if provided
                if (isset($data['stock_adjustment'])) {
                    $this->adjustStock($product, $data['stock_adjustment'], 'تعديل يدوي');
                }

                return $product;
            } catch (Exception $e) {
                throw new Exception('خطأ في تحديث المنتج: ' . $e->getMessage());
            }
        });
    }

    /**
     * Adjust product stock
     */
    public function adjustStock(Product $product, float $quantity, string $reason = ''): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $reason) {
            $type = $quantity > 0 ? StockMovement::TYPE_IN : StockMovement::TYPE_OUT;
            $absQuantity = abs($quantity);

            // Update product stock
            $product->update([
                'current_stock_kg' => max(0, $product->current_stock_kg + $quantity),
            ]);

            // Record movement
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity_kg' => $absQuantity,
                'reference_type' => 'adjustment',
                'notes' => $reason,
                'created_by' => Auth::id(),
            ]);

            return $movement;
        });
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(): array
    {
        $products = Product::lowStock()
            ->with(['mainCategory', 'subCategory', 'supplier'])
            ->get();

        return $products->map(function ($product) {
            $shortage = $product->minimum_stock_alert - $product->current_stock_kg;
            return [
                'product' => $product,
                'shortage' => $shortage,
                'alert_level' => $product->minimum_stock_alert,
                'current_stock' => $product->current_stock_kg,
            ];
        })->toArray();
    }

    /**
     * Get product with complete information
     */
    public function getProductDetails(Product $product): array
    {
        return [
            'product' => $product,
            'category' => $product->mainCategory,
            'sub_category' => $product->subCategory,
            'supplier' => $product->supplier,
            'profit_margin' => $product->getProfitMarginPercentage(),
            'is_low_stock' => $product->isLowStock(),
            'total_sold' => $product->invoiceItems()->sum('quantity_kg'),
            'total_purchased' => $product->purchaseItems()->sum('quantity_kg'),
            'stock_movements' => $product->stockMovements()
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    /**
     * Search products
     */
    public function searchProducts(string $term, int $perPage = 15): LengthAwarePaginator
    {
        return Product::search($term)
            ->with(['mainCategory', 'subCategory', 'supplier'])
            ->paginate($perPage);
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::byCategory($categoryId)
            ->with(['subCategory', 'supplier'])
            ->paginate($perPage);
    }

    /**
     * Get products by sub category
     */
    public function getProductsBySubCategory(int $subCategoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::bySubCategory($subCategoryId)
            ->with(['mainCategory', 'supplier'])
            ->paginate($perPage);
    }

    /**
     * Get products by supplier
     */
    public function getProductsBySupplier(int $supplierId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::bySupplier($supplierId)
            ->with(['mainCategory', 'subCategory'])
            ->paginate($perPage);
    }

    /**
     * Get all products for sale
     */
    public function getAllProductsForSale(int $perPage = 50): LengthAwarePaginator
    {
        return Product::with(['mainCategory', 'subCategory'])
            ->where('current_stock_kg', '>', 0)
            ->paginate($perPage);
    }

    /**
     * Get inventory summary
     */
    public function getInventorySummary(): array
    {
        $products = Product::all();

        return [
            'total_products' => $products->count(),
            'total_stock_kg' => $products->sum('current_stock_kg'),
            'total_low_stock' => $products->filter(fn($p) => $p->isLowStock())->count(),
            'total_value' => $products->sum(fn($p) => $p->current_stock_kg * $p->purchase_price_per_kg),
            'products' => $products,
        ];
    }

    /**
     * Update product prices
     */
    public function updatePrices(Product $product, float $purchasePrice, float $sellingPrice): Product
    {
        $product->update([
            'purchase_price_per_kg' => $purchasePrice,
            'selling_price_per_kg' => $sellingPrice,
        ]);

        return $product;
    }

    /**
     * Restore deleted product
     */
    public function restoreProduct(int $productId): Product
    {
        $product = Product::withTrashed()->findOrFail($productId);
        $product->restore();
        return $product;
    }

    /**
     * Permanently delete product (admin only)
     */
    public function permanentlyDeleteProduct(Product $product): void
    {
        $product->forceDelete();
    }

    /**
     * Generate SKU from product name and count
     */
    private function generateSku(string $productName): string
    {
        // Take first 4 characters of product name (Arabic or English)
        $prefix = substr(preg_replace('/[^a-zA-Z0-9آ-ي]/', '', $productName), 0, 4);
        if (empty($prefix)) {
            $prefix = 'PROD';
        }

        // Get count of products and add padding
        $count = Product::count() + 1;
        $sku = strtoupper($prefix) . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (Product::where('sku', $sku)->exists()) {
            $count++;
            $sku = strtoupper($prefix) . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return $sku;
    }

    /**
     * Generate SKU for display (public method for Controller)
     */
    public function generateSkuForDisplay(string $productName): string
    {
        return $this->generateSku($productName);
    }
}
