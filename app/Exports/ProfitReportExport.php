<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitReportExport implements FromCollection, WithHeadings, WithStyles
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
        $rows->push(['ملخص الأرباح']);
        $rows->push(['']);
        $rows->push(['الإيرادات الإجمالية', $this->data['revenue_total'] ?? 0]);
        $rows->push(['التكلفة الإجمالية', $this->data['total_cost'] ?? 0]);
        $rows->push(['إجمالي الأرباح', $this->data['total_profit'] ?? 0]);

        $rows->push(['']);
        $rows->push(['تفصيل المبيعات']);
        $rows->push(['']);

        // Sales breakdown
        if (isset($this->data['sales_breakdown'])) {
            foreach ($this->data['sales_breakdown'] as $sale) {
                $rows->push([
                    $sale['type'] ?? '',
                    $sale['revenue'] ?? 0,
                    $sale['cost'] ?? 0,
                    $sale['profit'] ?? 0,
                ]);
            }
        }

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

        return $rows;
    }

    public function headings(): array
    {
        return [
            'البيان',
            'المبلغ (ج.م)',
            'التكلفة (ج.م)',
            'الربح (ج.م)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2C1810']]],
        ];
    }
}
