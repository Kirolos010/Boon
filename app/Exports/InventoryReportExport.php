<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithHeadings, WithStyles
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
        $rows->push(['ملخص المخزون']);
        $rows->push(['']);
        $rows->push(['إجمالي المنتجات', $this->data['summary']['total_products'] ?? 0]);
        $rows->push(['إجمالي الكمية (كج)', $this->data['summary']['total_quantity_kg'] ?? 0]);
        $rows->push(['قيمة المخزون (ر.س)', $this->data['summary']['total_value'] ?? 0]);
        $rows->push(['منتجات الحد الأدنى', $this->data['summary']['low_stock_count'] ?? 0]);

        $rows->push(['']);
        $rows->push(['تفاصيل المخزون']);
        $rows->push(['']);

        // Product inventory details
        if (isset($this->data['products'])) {
            foreach ($this->data['products'] as $product) {
                $rows->push([
                    $product['name_ar'] ?? $product['name'] ?? '',
                    $product['category'] ?? '',
                    $product['current_stock_kg'] ?? 0,
                    $product['purchase_price_per_kg'] ?? 0,
                    $product['stock_value'] ?? 0,
                    $product['selling_price_per_kg'] ?? 0,
                    $product['profit_margin'] ?? 0,
                    $product['status'] ?? '',
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'المنتج',
            'الفئة',
            'الكمية (كج)',
            'سعر الشراء',
            'قيمة المخزون',
            'سعر البيع',
            'نسبة الربح %',
            'الحالة'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2C1810']]],
        ];
    }
}
