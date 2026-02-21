<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();

        // Add sales by product
        if (isset($this->data['by_product'])) {
            $rows->push(['===== المبيعات حسب المنتج =====']);
            $rows->push(['']);

            foreach ($this->data['by_product'] as $product) {
                $rows->push([
                    $product['product_name'] ?? '',
                    $product['quantity'] ?? 0,
                    $product['revenue'] ?? 0,
                    $product['cost'] ?? 0,
                    $product['profit'] ?? 0,
                    (($product['revenue'] ?? 0) > 0 ? (($product['profit'] ?? 0) / ($product['revenue'] ?? 0) * 100) : 0)
                ]);
            }

            $rows->push(['']);
            $rows->push(['===== المبيعات حسب العميل =====']);
            $rows->push(['']);

            // Add sales by client
            if (isset($this->data['by_client'])) {
                foreach ($this->data['by_client'] as $client) {
                    $rows->push([
                        $client['client_name'] ?? '',
                        $client['invoices_count'] ?? 0,
                        $client['total_amount'] ?? 0,
                        $client['paid_amount'] ?? 0,
                        $client['remaining_amount'] ?? 0,
                        (($client['total_amount'] ?? 0) > 0 ? (($client['paid_amount'] ?? 0) / ($client['total_amount'] ?? 0) * 100) : 0)
                    ]);
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'المنتج/العميل',
            'الكمية/الفواتير',
            'الإيرادات',
            'التكلفة',
            'الربح',
            'النسبة'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(12);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2C1810']]],
        ];
    }
}
