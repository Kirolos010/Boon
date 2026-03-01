<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class InvoiceService
{
    /**
     * Create a new invoice with items and handle stock deduction
     *
     * @param array $data - Invoice data including items
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            try {
                // Validate items exist
                if (empty($data['items']) || count($data['items']) === 0) {
                    throw new Exception('لا يمكن إنشاء فاتورة بدون عناصر');
                }

                // Generate invoice number
                $invoiceNumber = $this->generateInvoiceNumber();

                // Calculate totals
                $totals = $this->calculateInvoiceTotals($data['items'], $data['discount'] ?? 0);

                // Create invoice
                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'type' => $data['type'] ?? Invoice::TYPE_REGULAR,
                    'client_id' => $data['client_id'] ?? null,
                    'customer_name' => $data['customer_name'] ?? null,
                    'user_id' => Auth::id(),
                    'subtotal' => $totals['subtotal'],
                    'discount' => $data['discount'] ?? 0,
                    'tax' => $totals['tax'],
                    'total' => $totals['total'],
                    'amount_paid' => 0, // Initialize as 0, will be set by recordPayment if needed
                    'remaining_balance' => $totals['total'],
                    'status' => Invoice::STATUS_UNPAID,
                    'payment_method' => $data['payment_method'] ?? null,
                    'invoice_date' => $data['invoice_date'] ?? now(),
                    'notes' => $data['notes'] ?? null,
                ]);

                // Create invoice items and deduct stock
                foreach ($data['items'] as $item) {
                    $this->createInvoiceItem($invoice, $item);
                }

                // Calculate and store invoice-level profit snapshot
                $invoice->load('items');
                $totalCost = $invoice->items->sum(function ($item) {
                    return $item->cost_price_per_kg * $item->quantity_kg;
                });
                $grossProfit = $invoice->items->sum('item_profit');

                $invoice->update([
                    'total_cost' => round($totalCost, 3),
                    'gross_profit' => round($grossProfit, 3),
                ]);

                // Update client debt if client exists
                if ($invoice->client_id) {
                    $this->updateClientDebt($invoice->client, $invoice->remaining_balance);
                }

                // Record payment if amount paid > 0
                $amountPaid = (float) ($data['amount_paid'] ?? 0);
                if ($amountPaid > 0) {
                    // Ensure amount paid doesn't exceed total
                    $amountPaid = min($amountPaid, $invoice->total);
                    $this->recordPayment(
                        $invoice,
                        $amountPaid,
                        $data['payment_method'] ?? 'cash',
                        $data['payment_date'] ?? null,
                        $data['notes'] ?? null
                    );
                }

                return $invoice;
            } catch (Exception $e) {
                throw new Exception('خطأ في إنشاء الفاتورة: ' . $e->getMessage());
            }
        });
    }

    /**
     * Create invoice item and deduct stock
     */
    private function createInvoiceItem(Invoice $invoice, array $itemData): InvoiceItem
    {
        $product = Product::findOrFail($itemData['product_id']);

        // Validate stock availability
        if ($product->current_stock_kg < $itemData['quantity_kg']) {
            throw new Exception("المخزون غير كافي للمنتج: {$product->name_ar}");
        }

        // Calculate item total
        $unitPrice = $itemData['unit_price'] ?? $product->selling_price_per_kg;
        $itemTotal = $itemData['quantity_kg'] * $unitPrice;

        // Get cost price at time of purchase
        $costPrice = $product->purchase_price_per_kg;
        // Calculate profit for this item
        $itemProfit = ($unitPrice - $costPrice) * $itemData['quantity_kg'];

        // Create invoice item
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity_kg' => $itemData['quantity_kg'],
            'unit_price' => $unitPrice,
            'total' => $itemTotal,
            'cost_price_per_kg' => $costPrice,
            'item_profit' => $itemProfit,
        ]);

        // Deduct from product stock
        $product->update([
            'current_stock_kg' => $product->current_stock_kg - $itemData['quantity_kg'],
        ]);

        // Record stock movement
        StockMovement::create([
            'product_id' => $product->id,
            'type' => StockMovement::TYPE_OUT,
            'quantity_kg' => $itemData['quantity_kg'],
            'reference_type' => 'invoice',
            'reference_id' => $invoice->id,
            'notes' => "بيع عبر فاتورة #{$invoice->invoice_number}",
            'created_by' => Auth::id(),
        ]);

        return $item;
    }

    /**
     * Record payment for invoice
     */
    public function recordPayment(Invoice $invoice, float $amount, string $method = 'cash', ?string $paymentDate = null, ?string $notes = null): InvoicePayment
    {
        return DB::transaction(function () use ($invoice, $amount, $method, $paymentDate, $notes) {
            // Validate and sanitize amount
            $amount = (float) $amount;
            if ($amount <= 0) {
                throw new Exception('المبلغ المدفوع يجب أن يكون أكبر من صفر');
            }

            // Ensure amount paid doesn't exceed remaining balance
            $remainingBalance = max(0, ($invoice->total - $invoice->amount_paid));
            if ($amount > $remainingBalance) {
                throw new Exception('المبلغ المدفوع لا يمكن أن يتجاوز المبلغ المتبقي: ' . $remainingBalance);
            }

            // Create payment record with sanitized amount
            $payment = InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => round($amount, 3),
                'payment_date' => $paymentDate ? Carbon::parse($paymentDate)->toDateString() : now()->toDateString(),
                'payment_method' => $method,
                'notes' => $notes,
                'created_by' => Auth::id(),
            ]);

            // Update invoice amounts
            $newAmountPaid = (float) $invoice->amount_paid + (float) $amount;
            $newBalance = max(0, (float) $invoice->total - (float) $newAmountPaid);

            $invoice->update([
                'amount_paid' => round($newAmountPaid, 3),
                'remaining_balance' => round($newBalance, 3),
                'status' => $this->calculateStatus((float) $invoice->total, (float) $newAmountPaid),
            ]);

            // Update client debt
            if ($invoice->client_id) {
                $this->updateClientDebt($invoice->client, $newBalance);
            }

            return $payment;
        });
    }

    /**
     * Calculate invoice profit
     */
    public function calculateProfit($invoice): float
    {
        if (!$invoice) {
            return 0;
        }

        if (!is_null($invoice->gross_profit)) {
            return round((float) $invoice->gross_profit, 3);
        }

        $invoice->loadMissing('items.product');
        $profit = 0;

        foreach ($invoice->items as $item) {
            $costPrice = $item->cost_price_per_kg ?? $item->product->purchase_price_per_kg;
            $sellingPrice = $item->unit_price;
            $profit += ($sellingPrice - $costPrice) * $item->quantity_kg;
        }

        return round($profit, 3);
    }

    /**
     * Update invoice with new items and recalculate
     */
    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            // If items changed, restore old stock and deduct new stock
            if (isset($data['items'])) {
                // Restore old items
                foreach ($invoice->items as $oldItem) {
                    $oldItem->product->increment('current_stock_kg', $oldItem->quantity_kg);

                    // Remove old stock movement
                    StockMovement::where('reference_type', 'invoice')
                        ->where('reference_id', $invoice->id)
                        ->delete();
                }

                // Delete old items
                $invoice->items()->delete();

                // Create new items
                foreach ($data['items'] as $item) {
                    $this->createInvoiceItem($invoice, $item);
                }

                // Recalculate totals
                $totals = $this->calculateInvoiceTotals($data['items'], $data['discount'] ?? $invoice->discount);
                $invoice->load('items');
                $totalCost = $invoice->items->sum(function ($item) {
                    return $item->cost_price_per_kg * $item->quantity_kg;
                });
                $grossProfit = $invoice->items->sum('item_profit');

                $invoice->update([
                    'subtotal' => $totals['subtotal'],
                    'discount' => $data['discount'] ?? $invoice->discount,
                    'tax' => $totals['tax'],
                    'total' => $totals['total'],
                    'total_cost' => round($totalCost, 3),
                    'gross_profit' => round($grossProfit, 3),
                    'remaining_balance' => max(0, $totals['total'] - $invoice->amount_paid),
                    'status' => $this->calculateStatus($totals['total'], $invoice->amount_paid),
                ]);
            }

            return $invoice->refresh();
        });
    }

    /**
     * Create quick sale (walk-in customer)
     */
    public function createQuickSale(array $data): Invoice
    {
        // Quick sales don't have client, are always paid, and marked as quick type
        $data['type'] = Invoice::TYPE_QUICK;
        $data['client_id'] = null;
        $data['amount_paid'] = $this->calculateInvoiceTotals($data['items'], $data['discount'] ?? 0)['total'];
        $data['payment_method'] = $data['payment_method'] ?? 'cash';

        return $this->createInvoice($data);
    }

    /**
     * Calculate totals for invoice items
     */
    private function calculateInvoiceTotals(array $items, float $discount = 0): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $unitPrice = $item['unit_price'] ?? $product->selling_price_per_kg;
            $subtotal += $item['quantity_kg'] * $unitPrice;
        }

        // Calculate totals (Tax disabled - commented out)
        $afterDiscount = $subtotal - $discount;
        // $tax = $afterDiscount * 0.15;
        // $total = $afterDiscount + $tax;

        // Using afterDiscount as total without tax
        $tax = 0; // Tax disabled
        $total = $afterDiscount;

        return [
            'subtotal' => round($subtotal, 3),
            'tax' => round($tax, 3),
            'total' => round($total, 3),
        ];
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'INV-' . $date . '-';

        // Find the highest number for today
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the number from the last invoice
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate invoice status based on payment
     */
    private function calculateStatus(float $total, float $amountPaid): string
    {
        if ($amountPaid >= $total) {
            return Invoice::STATUS_PAID;
        } elseif ($amountPaid > 0) {
            return Invoice::STATUS_PARTIAL;
        }
        return Invoice::STATUS_UNPAID;
    }

    /**
     * Update client's total debt
     */
    private function updateClientDebt(Client $client, float $balance): void
    {
        $client->update([
            'total_debt' => $balance,
        ]);
    }

    /**
     * Get invoice details with items and profit
     */
    public function getInvoiceDetails(Invoice $invoice): array
    {
        return [
            'invoice' => $invoice,
            'items' => $invoice->items()->with('product')->get(),
            'profit' => $this->calculateProfit($invoice),
            'payments' => $invoice->payments,
            'client' => $invoice->client,
        ];
    }

    /**
     * Cancel invoice and restore stock
     */
    public function cancelInvoice(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            // Restore stock for all items
            foreach ($invoice->items as $item) {
                $item->product->increment('current_stock_kg', $item->quantity_kg);

                // Remove stock movement
                StockMovement::where('reference_type', 'invoice')
                    ->where('reference_id', $invoice->id)
                    ->delete();
            }

            // Soft delete invoice
            $invoice->delete();

            // Update client debt
            if ($invoice->client_id) {
                $client = $invoice->client;
                $remainingDebt = $client->invoices()
                    ->whereNotNull('remaining_balance')
                    ->sum('remaining_balance');

                $client->update(['total_debt' => $remainingDebt]);
            }
        });
    }

    /**
     * Get today's invoices summary
     */
    public function getTodaysSummary(): array
    {
        $invoices = Invoice::today()->get();

        return [
            'total_invoices' => $invoices->count(),
            'total_sales' => $invoices->sum('total'),
            'total_profit' => $invoices->map(fn($inv) => $this->calculateProfit($inv))->sum(),
            'total_paid' => $invoices->sum('amount_paid'),
            'total_pending' => $invoices->sum('remaining_balance'),
        ];
    }
}
