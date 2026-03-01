<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\InvoiceService;
use App\Services\ProductService;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Client;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected InvoiceService $invoiceService,
        protected ProductService $productService
    ) {
    }

    public function index()
    {
        $today = Carbon::now()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $todaySummary = $this->invoiceService->getTodaysSummary();
        $dailyReport = $this->reportService->dailyClosingReport($today);
        $monthlyTrend = $this->reportService->monthlySalesTrend(12);
        $salesByProduct = $this->reportService->salesByProduct($startOfMonth, $endOfMonth);
        $topProductsData = $this->reportService->topPerformingProducts(5);
        $topClientsData = $this->reportService->topClients(5);
        $topProducts = collect($topProductsData['data'] ?? []);
        $topClients = collect($topClientsData['data'] ?? []);
        $lowStockProducts = collect($this->productService->getLowStockProducts());
        $inventorySummary = $this->productService->getInventorySummary();
        $profitReport = $this->reportService->profitReport($startOfMonth, $endOfMonth);

        $stats = [
            'total_invoices' => Invoice::where('type', 'regular')->count(),
            'quick_sales' => Invoice::where('type', 'quick')->count(),
            'total_clients' => Client::count(),
            'total_products' => Product::count(),
            'pending_purchases' => Purchase::where('status', 'pending')->count(),
            'pending_payments' => Invoice::where('status', 'unpaid')->orWhere('status', 'partial')->count(),
        ];

        return view('dashboard.index', [
            'today_summary' => $todaySummary,
            'daily_report' => $dailyReport,
            'monthly_trend' => $monthlyTrend,
            'sales_by_product' => $salesByProduct,
            'top_products' => $topProducts,
            'top_clients' => $topClients,
            'low_stock_products' => $lowStockProducts,
            'inventory_summary' => $inventorySummary,
            'profit_report' => $profitReport,
            'stats' => $stats,
        ]);
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        $salesByProductReport = $this->reportService->salesByProduct($startDate, $endDate);
        $salesByClientReport = $this->reportService->salesByClient($startDate, $endDate);
        $salesByCategoryReport = $this->reportService->salesByCategory($startDate, $endDate);

        return view('reports.sales', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'salesByProduct' => collect($salesByProductReport['data'] ?? []),
            'salesByClient' => collect($salesByClientReport['data'] ?? []),
            'salesByCategory' => collect($salesByCategoryReport['data'] ?? []),
        ]);
    }

    public function profitReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        $profitReport = $this->reportService->profitReport($startDate, $endDate);

        return view('reports.profit', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'profitReport' => $profitReport,
        ]);
    }

    public function inventoryReport()
    {
        $inventoryReport = $this->reportService->inventoryReport();

        return view('reports.inventory', [
            'inventoryReport' => $inventoryReport,
        ]);
    }

    public function dailyClosingReport(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date'))->toDateString() : Carbon::now()->toDateString();
        $dailyClosing = $this->reportService->dailyClosingReport($date);

        return view('reports.daily-closing', [
            'date' => $date,
            'dailyClosing' => $dailyClosing,
        ]);
    }
}
