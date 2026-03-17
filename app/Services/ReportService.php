<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Sales by product report
     */
    public function salesByProduct($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        $sales = InvoiceItem::whereHas('invoice', function ($q) use ($startDate, $endDate) {
            $q->dateBetween($startDate->toDateString(), $endDate->toDateString());
        })
            ->with('product:id,name,name_ar,purchase_price_per_kg,selling_price_per_kg')
            ->get()
            ->groupBy('product_id');

        $report = [];
        foreach ($sales as $productId => $items) {
            $product = $items->first()->product;
            $totalQty = $items->sum('quantity_kg');
            $totalRevenue = $items->sum('total');
            $totalCost = $items->sum(fn($item) => $item->quantity_kg * $product->purchase_price_per_kg);
            $profit = $totalRevenue - $totalCost;

            $report[] = [
                'product_id' => $productId,
                'product_name' => $product->name,
                'product_name_ar' => $product->name_ar,
                'total_quantity_kg' => round($totalQty, 3),
                'total_revenue' => round($totalRevenue, 3),
                'total_cost' => round($totalCost, 3),
                'profit' => round($profit, 3),
                'profit_margin' => $totalRevenue > 0 ? round(($profit / $totalRevenue) * 100, 2) : 0,
                'average_price' => $totalQty > 0 ? round($totalRevenue / $totalQty, 3) : 0,
            ];
        }

        // Sort by total revenue descending
        usort($report, fn($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'data' => $report,
            'totals' => [
                'total_quantity_kg' => collect($report)->sum('total_quantity_kg'),
                'total_revenue' => collect($report)->sum('total_revenue'),
                'total_cost' => collect($report)->sum('total_cost'),
                'total_profit' => collect($report)->sum('profit'),
            ],
        ];
    }

    /**
     * Sales by client report
     */
    public function salesByClient($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        $invoices = Invoice::dateBetween($startDate->toDateString(), $endDate->toDateString())
            ->regular()
            ->with([
                'client' => function ($query) {
                    $query->withTrashed();
                },
                'items',
            ])
            ->get()
            ->groupBy('client_id');

        $report = [];
        foreach ($invoices as $clientId => $clientInvoices) {
            $client = $clientInvoices->first()->client;
            $totalInvoices = $clientInvoices->count();
            $totalAmount = $clientInvoices->sum('total');
            $totalPaid = $clientInvoices->sum('amount_paid');
            $totalPending = $totalAmount - $totalPaid;

            $report[] = [
                'client_id' => $clientId,
                'client_name' => $client->name_ar??$client->name ?? 'غير معروف',
                'client_name_ar' => $client->name_ar ??  null,
                'phone' => $client->phone ?? null,
                'total_invoices' => $totalInvoices,
                'total_amount' => round($totalAmount, 3),
                'total_paid' => round($totalPaid, 3),
                'total_pending' => round($totalPending, 3),
                'payment_rate' => $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 2) : 0,
            ];
        }

        // Sort by total amount descending
        usort($report, fn($a, $b) => $b['total_amount'] <=> $a['total_amount']);

        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'data' => $report,
            'totals' => [
                'total_clients' => count($report),
                'total_amount' => collect($report)->sum('total_amount'),
                'total_paid' => collect($report)->sum('total_paid'),
                'total_pending' => collect($report)->sum('total_pending'),
            ],
        ];
    }

    /**
     * Sales by category report
     */
    public function salesByCategory($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        $sales = InvoiceItem::whereHas('invoice', function ($q) use ($startDate, $endDate) {
            $q->dateBetween($startDate->toDateString(), $endDate->toDateString());
        })
            ->with('product.mainCategory')
            ->get()
            ->groupBy(fn($item) => $item->product->main_category_id);

        $report = [];
        foreach ($sales as $categoryId => $items) {
            $category = $items->first()->product->mainCategory;
            $totalQty = $items->sum('quantity_kg');
            $totalRevenue = $items->sum('total');
            $totalCost = $items->sum(fn($item) => $item->quantity_kg * $item->product->purchase_price_per_kg);
            $profit = $totalRevenue - $totalCost;

            $report[] = [
                'category_id' => $categoryId,
                'category_name' => $category->name,
                'category_name_ar' => $category->name_ar,
                'total_quantity_kg' => round($totalQty, 3),
                'total_revenue' => round($totalRevenue, 3),
                'total_cost' => round($totalCost, 3),
                'profit' => round($profit, 3),
                'profit_margin' => $totalRevenue > 0 ? round(($profit / $totalRevenue) * 100, 2) : 0,
                'items_count' => $items->count(),
            ];
        }

        // Sort by revenue descending
        usort($report, fn($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'data' => $report,
            'totals' => [
                'total_revenue' => collect($report)->sum('total_revenue'),
                'total_cost' => collect($report)->sum('total_cost'),
                'total_profit' => collect($report)->sum('profit'),
            ],
        ];
    }

    /**
     * Profit report with detailed breakdown
     */
    public function profitReport($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        // Regular invoices
        $regularInvoices = Invoice::regular()
            ->dateBetween($startDate->toDateString(), $endDate->toDateString())
            ->with('items.product')
            ->get();

        $regularRevenue = 0;
        $regularCost = 0;
        $regularProfit = 0;

        foreach ($regularInvoices as $invoice) {
            $revenue = $invoice->total;
            $regularRevenue += $revenue;
            $profit = $invoice->gross_profit ?? $invoice->items->sum(function ($item) {
                $costPrice = $item->cost_price_per_kg ?? ($item->product->purchase_price_per_kg ?? 0);
                return ($item->unit_price - $costPrice) * $item->quantity_kg;
            });
            $regularProfit += $profit;
            $regularCost += $invoice->total_cost ?? ($revenue - $profit);
        }

        // Quick sales
        $quickSales = Invoice::quickSales()
            ->dateBetween($startDate->toDateString(), $endDate->toDateString())
            ->with('items.product')
            ->get();

        $quickRevenue = 0;
        $quickCost = 0;
        $quickProfit = 0;

        foreach ($quickSales as $invoice) {
            $revenue = $invoice->total;
            $quickRevenue += $revenue;
            $profit = $invoice->gross_profit ?? $invoice->items->sum(function ($item) {
                $costPrice = $item->cost_price_per_kg ?? ($item->product->purchase_price_per_kg ?? 0);
                return ($item->unit_price - $costPrice) * $item->quantity_kg;
            });
            $quickProfit += $profit;
            $quickCost += $invoice->total_cost ?? ($revenue - $profit);
        }

        $totalRevenue = $regularRevenue + $quickRevenue;
        $totalCost = $regularCost + $quickCost;
        $totalProfit = $regularProfit + $quickProfit;

        // Get expenses for period
        $expenses = Expense::dateBetween($startDate->toDateString(), $endDate->toDateString())
            ->get()
            ->groupBy('expense_category_id');

        $expensesByCategory = [];
        $totalExpenses = 0;

        foreach ($expenses as $categoryId => $categoryExpenses) {
            $category = $categoryExpenses->first()->category;
            $categoryTotal = $categoryExpenses->sum('amount');
            $totalExpenses += $categoryTotal;

            $expensesByCategory[] = [
                'category_name' => $category->name,
                'category_name_ar' => $category->name_ar,
                'amount' => round($categoryTotal, 3),
                'count' => $categoryExpenses->count(),
                'percentage' => 0, // Will be calculated after total is known
            ];
        }

        // Calculate percentages
        foreach ($expensesByCategory as &$expense) {
            $expense['percentage'] = $totalExpenses > 0 ? round(($expense['amount'] / $totalExpenses) * 100, 2) : 0;
        }

        $netProfit = $totalProfit - $totalExpenses;

        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'sales_data' => [
                'total_revenue' => round($totalRevenue, 3),
                'total_cost' => round($totalCost, 3),
                'gross_profit' => round($totalProfit, 3),
                'gross_profit_margin' => $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 2) : 0,
                'regular_invoices_count' => $regularInvoices->count(),
                'regular_revenue' => round($regularRevenue, 3),
                'regular_cost' => round($regularCost, 3),
                'quick_sales_count' => $quickSales->count(),
                'quick_revenue' => round($quickRevenue, 3),
                'quick_cost' => round($quickCost, 3),
            ],
            'expenses_data' => $expensesByCategory,
            'net_data' => [
                'gross_profit' => round($totalProfit, 3),
                'total_expenses' => round($totalExpenses, 3),
                'net_profit' => round($netProfit, 3),
                'net_profit_margin' => $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0,
            ],
            'summary' => [
                'total_invoices' => $regularInvoices->count() + $quickSales->count(),
                'total_expenses_transactions' => Expense::dateBetween($startDate->toDateString(), $endDate->toDateString())->count(),
            ],
        ];
    }

    /**
     * Inventory report
     */
    public function inventoryReport(): array
    {
        $products = Product::with(['mainCategory', 'subCategory', 'supplier'])
            ->get();

        $totalValue = 0;
        $lowStockCount = 0;
        $productsData = [];

        foreach ($products as $product) {
            $value = $product->current_stock_kg * $product->purchase_price_per_kg;
            $totalValue += $value;
            $isLowStock = $product->current_stock_kg <= $product->minimum_stock_alert;
            $profitMargin = $product->purchase_price_per_kg > 0
                ? (($product->selling_price_per_kg - $product->purchase_price_per_kg) / $product->purchase_price_per_kg) * 100
                : 0;

            if ($isLowStock) {
                $lowStockCount++;
            }

            $productsData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_name_ar' => $product->name_ar,
                'sku' => $product->sku,
                'category' => $product->mainCategory->name_ar,
                'current_stock_kg' => round($product->current_stock_kg, 3),
                'minimum_alert' => round($product->minimum_stock_alert, 3),
                'status' => $isLowStock ? 'low' : 'ok',
                'purchase_price_per_kg' => round($product->purchase_price_per_kg, 3),
                'selling_price_per_kg' => round($product->selling_price_per_kg, 3),
                'stock_value' => round($value, 3),
                'profit_margin' => round($profitMargin, 2),
            ];
        }

        return [
            'generated_at' => now()->toDateTimeString(),
            'summary' => [
                'total_products' => $products->count(),
                'total_stock_kg' => round($products->sum('current_stock_kg'), 3),
                'total_value' => round($totalValue, 3),
                'low_stock_count' => $lowStockCount,
            ],
            'products' => $productsData,
        ];
    }

    /**
     * Daily closing report
     */
    public function dailyClosingReport($date = null): array
    {
        $date = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();

        $invoices = Invoice::whereDate('invoice_date', $date)->get();
        $quickSales = $invoices->where('type', 'quick');
        $regularInvoices = $invoices->where('type', 'regular');

        $purchases = Purchase::whereDate('purchase_date', $date)->get();
        $expenses = Expense::whereDate('expense_date', $date)->get();

        $totalRevenue = $invoices->sum('total');
        $totalPaid = $invoices->sum('amount_paid');
        $totalPending = $invoices->sum('remaining_balance');
        $totalProfit = $invoices->sum(function ($inv) {
            return $inv->gross_profit ?? $inv->items->sum(function ($item) {
                $costPrice = $item->cost_price_per_kg ?? ($item->product->purchase_price_per_kg ?? 0);
                return ($item->unit_price - $costPrice) * $item->quantity_kg;
            });
        });
        $totalExpenses = $expenses->sum('amount');

        return [
            'date' => $date,
            'sales_summary' => [
                'quick_sales_count' => $quickSales->count(),
                'quick_sales_amount' => round($quickSales->sum('total'), 3),
                'regular_invoices_count' => $regularInvoices->count(),
                'regular_invoices_amount' => round($regularInvoices->sum('total'), 3),
                'total_invoices' => $invoices->count(),
                'total_revenue' => round($totalRevenue, 3),
                'total_paid' => round($totalPaid, 3),
                'total_pending' => round($totalPending, 3),
            ],
            'profit_summary' => [
                'gross_profit' => round($totalProfit, 3),
                'expenses' => round($totalExpenses, 3),
                'net_profit' => round($totalProfit - $totalExpenses, 3),
            ],
            'purchases_summary' => [
                'total_purchases' => $purchases->count(),
                'total_cost' => round($purchases->sum('total_cost'), 3),
                'pending_count' => $purchases->where('status', 'pending')->count(),
            ],
            'expenses_summary' => [
                'total_transactions' => $expenses->count(),
                'total_amount' => round($totalExpenses, 3),
                'by_category' => $expenses->groupBy('expense_category_id')->map(function ($group) {
                    return [
                        'category' => $group->first()->category->name_ar,
                        'total' => round($group->sum('amount'), 3),
                        'count' => $group->count(),
                    ];
                })->values()->toArray(),
            ],
            'cash_flow' => [
                'cash_in' => round($totalPaid, 3),
                'cash_out' => round($totalExpenses + $purchases->sum('total_cost'), 3),
                'net_cash' => round($totalPaid - ($totalExpenses + $purchases->sum('total_cost')), 3),
            ],
        ];
    }

    /**
     * Monthly sales trend report
     */
    public function monthlySalesTrend(int $months = 12): array
    {
        $trend = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStr = $month->format('Y-m');

            $invoices = Invoice::whereYear('invoice_date', $month->year)
                ->whereMonth('invoice_date', $month->month)
                ->get();

            $totalRevenue = $invoices->sum('total');
            $totalProfit = $invoices->sum(function ($inv) {
                return $inv->gross_profit ?? $inv->items->sum(function ($item) {
                    $costPrice = $item->cost_price_per_kg ?? ($item->product->purchase_price_per_kg ?? 0);
                    return ($item->unit_price - $costPrice) * $item->quantity_kg;
                });
            });

            $trend[] = [
                'month' => $monthStr,
                'month_ar' => $this->getArabicMonth($month),
                'revenue' => round($totalRevenue, 3),
                'profit' => round($totalProfit, 3),
                'invoice_count' => $invoices->count(),
            ];
        }

        return [
            'period_months' => $months,
            'data' => $trend,
            'totals' => [
                'total_revenue' => collect($trend)->sum('revenue'),
                'total_profit' => collect($trend)->sum('profit'),
                'average_monthly_revenue' => collect($trend)->avg('revenue'),
                'average_monthly_profit' => collect($trend)->avg('profit'),
            ],
        ];
    }

    /**
     * Get Arabic month name
     */
    private function getArabicMonth(Carbon $date): string
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر',
        ];

        return $months[$date->month] . ' ' . $date->year;
    }

    /**
     * Top performing products
     */
    public function topPerformingProducts(int $limit = 10, $startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        $sales = $this->salesByProduct($startDate, $endDate);

        return [
            'period' => $sales['period'],
            'data' => array_slice($sales['data'], 0, $limit),
        ];
    }

    /**
     * Top clients report
     */
    public function topClients(int $limit = 10, $startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        $sales = $this->salesByClient($startDate, $endDate);

        return [
            'period' => $sales['period'],
            'data' => array_slice($sales['data'], 0, $limit),
        ];
    }

    /**
     * Combined sales report
     */
    public function salesReport($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : now()->endOfMonth();

        $byProduct = $this->salesByProduct($startDate, $endDate);
        $byClient = $this->salesByClient($startDate, $endDate);
        $byCategory = $this->salesByCategory($startDate, $endDate);

        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'by_product' => $byProduct['data'] ?? [],
            'by_client' => $byClient['data'] ?? [],
            'by_category' => $byCategory['data'] ?? [],
            'totals' => $byProduct['totals'] ?? [],
        ];
    }
}
