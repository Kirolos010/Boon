<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyClosingExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();

        // Summary section
        $rows->push(['تقرير الإغلاق اليومي']);
        $rows->push(['']);
        $rows->push(['إجمالي الإيرادات', $this->data['revenue_total'] ?? 0]);
        $rows->push(['إجمالي التكلفة', $this->data['total_cost'] ?? 0]);
        $rows->push(['إجمالي الأرباح', $this->data['profit_total'] ?? 0]);
        $rows->push(['إجمالي المصروفات', $this->data['expenses_total'] ?? 0]);
        $rows->push(['صافي الربح', $this->data['net_profit'] ?? 0]);

        $rows->push(['']);
        $rows->push(['الفواتير العادية']);
        $rows->push(['']);
        $rows->push(['عدد الفواتير', $this->data['invoices_count'] ?? 0]);
        $rows->push(['الإيرادات', $this->data['invoices_revenue'] ?? 0]);
        $rows->push(['التكلفة', $this->data['invoices_cost'] ?? 0]);
        $rows->push(['الربح', $this->data['invoices_profit'] ?? 0]);

        $rows->push(['']);
        $rows->push(['المبيعات السريعة']);
        $rows->push(['']);
        $rows->push(['عدد العمليات', $this->data['quick_sales_count'] ?? 0]);
        $rows->push(['الإيرادات', $this->data['quick_sales_revenue'] ?? 0]);
        $rows->push(['التكلفة', $this->data['quick_sales_cost'] ?? 0]);
        $rows->push(['الربح', $this->data['quick_sales_profit'] ?? 0]);

        $rows->push(['']);
        $rows->push(['المصروفات حسب الفئة']);
        $rows->push(['']);

        // Expenses by category
        if (isset($this->data['expenses_by_category'])) {
            foreach ($this->data['expenses_by_category'] as $expense) {
                $rows->push([
                    $expense['category_name'] ?? $expense['category'] ?? '',
                    $expense['total'] ?? 0,
                ]);
            }
        }

        $rows->push(['']);
        $rows->push(['حالة الدفع']);
        $rows->push(['']);

        // Payment status
        if (isset($this->data['payment_status'])) {
            $rows->push(['نقداً', $this->data['payment_status']['cash'] ?? 0]);
            $rows->push(['شيك', $this->data['payment_status']['check'] ?? 0]);
            $rows->push(['على الحساب', $this->data['payment_status']['credit'] ?? 0]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['البيان', 'المبلغ (ج.م)'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2C1810']]],
        ];
    }
}
