<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class PurchaseService
{
    /**
     * Create a new purchase order
     */
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            try {
                // Validate items exist
                if (empty($data['items']) || count($data['items']) === 0) {
                    throw new Exception('لا يمكن إنشاء طلب شراء بدون عناصر');
                }

                // Generate purchase number
                $purchaseNumber = $this->generatePurchaseNumber();

                // Calculate totals
                $totals = $this->calculatePurchaseTotals($data['items']);

                // Create purchase
                $purchase = Purchase::create([
                    'purchase_number' => $purchaseNumber,
                    'supplier_id' => $data['supplier_id'],
                    'user_id' => Auth::id(),
                    'subtotal' => $totals['subtotal'],
                    'tax' => $data['tax'] ?? 0,
                    'total_cost' => $totals['total'],
                    'purchase_date' => $data['purchase_date'] ?? now()->toDateString(),
                    'status' => Purchase::STATUS_PENDING,
                    'notes' => $data['notes'] ?? null,
                ]);

                // Create purchase items
                foreach ($data['items'] as $item) {
                    $this->createPurchaseItem($purchase, $item);
                }

                return $purchase;
            } catch (Exception $e) {
                throw new Exception('خطأ في إنشاء طلب الشراء: ' . $e->getMessage());
            }
        });
    }

    /**
     * Create purchase item
     */
    private function createPurchaseItem(Purchase $purchase, array $itemData): PurchaseItem
    {
        $product = Product::findOrFail($itemData['product_id']);

        $itemTotal = $itemData['quantity_kg'] * $itemData['cost_per_kg'];

        return PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity_kg' => $itemData['quantity_kg'],
            'cost_per_kg' => $itemData['cost_per_kg'],
            'total_cost' => $itemTotal,
        ]);
    }

    /**
     * Receive purchase order and add to stock
     */
    public function receivePurchase(Purchase $purchase, array $receivedItems = []): Purchase
    {
        return DB::transaction(function () use ($purchase, $receivedItems) {
            try {
                // If receiving partial items, use provided items, otherwise receive all
                $itemsToReceive = !empty($receivedItems) ? $receivedItems : $purchase->items->toArray();

                foreach ($itemsToReceive as $item) {
                    $this->receiveItem($purchase, $item);
                }

                // Update purchase status
                $allReceived = $purchase->items->every(function ($item) {
                    return $item->quantity_kg == ($item->quantity_kg ?? 0);
                });

                $purchase->update([
                    'status' => $allReceived ? Purchase::STATUS_RECEIVED : Purchase::STATUS_PARTIAL,
                ]);

                return $purchase;
            } catch (Exception $e) {
                throw new Exception('خطأ في استقبال الطلب: ' . $e->getMessage());
            }
        });
    }

    /**
     * Receive single purchase item
     */
    private function receiveItem(Purchase $purchase, array $item): void
    {
        $purchaseItem = PurchaseItem::findOrFail($item['id'] ?? $item['purchase_item_id']);
        $product = $purchaseItem->product;
        $quantity = $item['received_quantity'] ?? $purchaseItem->quantity_kg;

        // Update product stock
        $product->increment('current_stock_kg', $quantity);

        // Update product's purchase price with the latest cost
        $product->update([
            'purchase_price_per_kg' => $purchaseItem->cost_per_kg,
        ]);

        // Record stock movement
        StockMovement::create([
            'product_id' => $product->id,
            'type' => StockMovement::TYPE_IN,
            'quantity_kg' => $quantity,
            'reference_type' => 'purchase',
            'reference_id' => $purchase->id,
            'notes' => "استقبال من طلب شراء #{$purchase->purchase_number}",
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Calculate profit from a purchase (selling all items at their prices)
     */
    public function calculatePotentialProfit(Purchase $purchase): float
    {
        $profit = 0;

        foreach ($purchase->items as $item) {
            $costPrice = $item->cost_per_kg;
            $sellingPrice = $item->product->selling_price_per_kg;
            $profit += ($sellingPrice - $costPrice) * $item->quantity_kg;
        }

        return round($profit, 3);
    }

    /**
     * Calculate actual profit from purchase (only sold items)
     */
    public function calculateActualProfit(Purchase $purchase): float
    {
        $profit = 0;

        foreach ($purchase->items as $purchaseItem) {
            // Get all sales of this product from invoices after this purchase
            $soldItems = $purchaseItem->product->invoiceItems()
                ->where('created_at', '>=', $purchase->created_at)
                ->get();

            foreach ($soldItems as $soldItem) {
                $costPrice = $purchaseItem->cost_per_kg;
                $sellingPrice = $soldItem->unit_price;

                // Calculate profit only for quantity available from this purchase
                // (Simplified: assume FIFO)
                $profit += ($sellingPrice - $costPrice) * $soldItem->quantity_kg;
            }
        }

        return round($profit, 3);
    }

    /**
     * Get purchase details
     */
    public function getPurchaseDetails(Purchase $purchase): array
    {
        return [
            'purchase' => $purchase,
            'items' => $purchase->items()->with('product')->get(),
            'supplier' => $purchase->supplier,
            'potential_profit' => $this->calculatePotentialProfit($purchase),
            'markup_percentage' => $this->calculateMarkupPercentage($purchase),
            'total_items' => $purchase->items->sum('quantity_kg'),
        ];
    }

    /**
     * Calculate markup percentage
     */
    public function calculateMarkupPercentage(Purchase $purchase): float
    {
        $totalCost = $purchase->total_cost;
        if ($totalCost == 0) return 0;

        $totalValue = $purchase->items->sum(function ($item) {
            return $item->product->selling_price_per_kg * $item->quantity_kg;
        });

        $markup = (($totalValue - $totalCost) / $totalCost) * 100;
        return round($markup, 2);
    }

    /**
     * Filter purchases by date range
     */
    public function getPurchasesByDateRange($startDate, $endDate, int $perPage = 15)
    {
        return Purchase::dateBetween($startDate, $endDate)
            ->with(['supplier', 'items'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Filter purchases by supplier
     */
    public function getPurchasesBySupplier(int $supplierId, int $perPage = 15)
    {
        return Purchase::bySupplier($supplierId)
            ->with(['items'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Filter purchases by status
     */
    public function getPurchasesByStatus(string $status, int $perPage = 15)
    {
        return Purchase::byStatus($status)
            ->with(['supplier', 'items'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get pending purchases
     */
    public function getPendingPurchases()
    {
        return Purchase::pending()
            ->with(['supplier', 'items'])
            ->latest()
            ->get();
    }

    /**
     * Calculate purchase totals
     */
    private function calculatePurchaseTotals(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['quantity_kg'] * $item['cost_per_kg'];
        }

        $total = $subtotal;

        return [
            'subtotal' => round($subtotal, 3),
            'total' => round($total, 3),
        ];
    }

    /**
     * Generate unique purchase number
     */
    private function generatePurchaseNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Purchase::whereDate('created_at', now())->count() + 1;
        return 'PUR-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cancel purchase and revert stock (if already received)
     */
    public function cancelPurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status === Purchase::STATUS_RECEIVED || $purchase->status === Purchase::STATUS_PARTIAL) {
                // Revert stock for all items
                foreach ($purchase->items as $item) {
                    $item->product->decrement('current_stock_kg', $item->quantity_kg);

                    // Remove stock movement
                    StockMovement::where('reference_type', 'purchase')
                        ->where('reference_id', $purchase->id)
                        ->delete();
                }
            }

            // Soft delete purchase
            $purchase->delete();
        });
    }

    /**
     * Get purchase statistics
     */
    public function getPurchaseStatistics($startDate = null, $endDate = null): array
    {
        $query = Purchase::query();

        if ($startDate && $endDate) {
            $query->dateBetween($startDate, $endDate);
        }

        $purchases = $query->get();

        return [
            'total_purchases' => $purchases->count(),
            'total_cost' => $purchases->sum('total_cost'),
            'total_items_kg' => $purchases->map(fn($p) => $p->items->sum('quantity_kg'))->sum(),
            'average_purchase_cost' => $purchases->avg('total_cost'),
            'pending_count' => $purchases->where('status', Purchase::STATUS_PENDING)->count(),
            'received_count' => $purchases->where('status', Purchase::STATUS_RECEIVED)->count(),
            'by_status' => [
                'pending' => $purchases->where('status', Purchase::STATUS_PENDING)->count(),
                'received' => $purchases->where('status', Purchase::STATUS_RECEIVED)->count(),
                'partial' => $purchases->where('status', Purchase::STATUS_PARTIAL)->count(),
            ],
        ];
    }
}
