<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Exports\SalesReportExport;
use App\Exports\ProfitReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\DailyClosingExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request, string $type)
    {
        try {
            $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
            $toDate = $request->input('to_date', now()->toDateString());

            switch ($type) {
                case 'sales':
                    $data = $this->reportService->salesReport($fromDate, $toDate);
                    $pdf = Pdf::loadView('reports.exports.sales-pdf', compact('data'))
                        ->setPaper('a4', 'landscape');
                    return $pdf->download('تقرير-المبيعات-' . now()->format('Y-m-d') . '.pdf');

                case 'profit':
                    $data = $this->reportService->profitReport($fromDate, $toDate);
                    $pdf = Pdf::loadView('reports.exports.profit-pdf', compact('data'))
                        ->setPaper('a4', 'landscape');
                    return $pdf->download('تقرير-الأرباح-' . now()->format('Y-m-d') . '.pdf');

                case 'inventory':
                    $data = $this->reportService->inventoryReport();
                    $pdf = Pdf::loadView('reports.exports.inventory-pdf', compact('data'))
                        ->setPaper('a4', 'landscape');
                    return $pdf->download('تقرير-المخزون-' . now()->format('Y-m-d') . '.pdf');

                case 'daily-closing':
                    $data = $this->reportService->dailyClosingReport(
                        $request->input('date', now()->toDateString())
                    );
                    $pdf = Pdf::loadView('reports.exports.daily-closing-pdf', compact('data'))
                        ->setPaper('a4', 'landscape');
                    return $pdf->download('تقرير-الإغلاق-اليومي-' . now()->format('Y-m-d') . '.pdf');

                default:
                    return back()->withErrors('نوع التقرير غير صحيح');
            }
        } catch (\Exception $e) {
            return back()->withErrors('خطأ في إنشاء ملف PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request, string $type)
    {
        try {
            $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
            $toDate = $request->input('to_date', now()->toDateString());

            switch ($type) {
                case 'sales':
                    $data = $this->reportService->salesReport($fromDate, $toDate);
                    return Excel::download(
                        new SalesReportExport($data),
                        'تقرير-المبيعات-' . now()->format('Y-m-d') . '.xlsx'
                    );

                case 'profit':
                    $data = $this->reportService->profitReport($fromDate, $toDate);
                    return Excel::download(
                        new ProfitReportExport($data),
                        'تقرير-الأرباح-' . now()->format('Y-m-d') . '.xlsx'
                    );

                case 'inventory':
                    $data = $this->reportService->inventoryReport();
                    return Excel::download(
                        new InventoryReportExport($data),
                        'تقرير-المخزون-' . now()->format('Y-m-d') . '.xlsx'
                    );

                case 'daily-closing':
                    $data = $this->reportService->dailyClosingReport(
                        $request->input('date', now()->toDateString())
                    );
                    return Excel::download(
                        new DailyClosingExport($data),
                        'تقرير-الإغلاق-اليومي-' . now()->format('Y-m-d') . '.xlsx'
                    );

                default:
                    return back()->withErrors('نوع التقرير غير صحيح');
            }
        } catch (\Exception $e) {
            return back()->withErrors('خطأ في إنشاء ملف Excel: ' . $e->getMessage());
        }
    }
}
