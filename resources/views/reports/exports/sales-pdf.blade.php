<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المبيعات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            direction: rtl;
        }

        .header {
            background-color: #2C1810;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            opacity: 0.9;
        }

        .date-range {
            background-color: #f5e6d3;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 4px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            background-color: #6F4E37;
            color: white;
            padding: 12px;
            margin-bottom: 10px;
            font-weight: bold;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #f5e6d3;
            padding: 10px;
            text-align: right;
            border-bottom: 2px solid #2C1810;
            font-weight: bold;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .number {
            text-align: left;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #2C1810;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .summary-box {
            background-color: #f5e6d3;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            border-left: 4px solid #D4AF37;
        }

        .summary-box .row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item label {
            font-size: 11px;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #2C1810;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير المبيعات</h1>
        <p>تحليل تفصيلي للمبيعات حسب المنتجات والعملاء</p>
    </div>

    @if(isset($data['date_range']))
        <div class="date-range">
            من {{ $data['date_range']['from'] }} إلى {{ $data['date_range']['to'] }}
        </div>
    @endif

    <!-- Sales by Product -->
    @if(isset($data['by_product']) && count($data['by_product']) > 0)
        <div class="section">
            <div class="section-title">المبيعات حسب المنتج</div>
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th class="number">الكمية</th>
                        <th class="number">الإيرادات</th>
                        <th class="number">التكلفة</th>
                        <th class="number">الربح</th>
                        <th class="number">نسبة الربح</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_product'] as $product)
                        <tr>
                            <td>{{ $product['product_name'] ?? '' }}</td>
                            <td class="number">{{ number_format($product['quantity'] ?? 0, 2) }}</td>
                            <td class="number">{{ number_format($product['revenue'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($product['cost'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($product['profit'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format((($product['revenue'] ?? 0) > 0 ? (($product['profit'] ?? 0) / ($product['revenue'] ?? 0) * 100) : 0), 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Sales by Client -->
    @if(isset($data['by_client']) && count($data['by_client']) > 0)
        <div class="section">
            <div class="section-title">المبيعات حسب العميل</div>
            <table>
                <thead>
                    <tr>
                        <th>العميل</th>
                        <th class="number">عدد الفواتير</th>
                        <th class="number">الإجمالي</th>
                        <th class="number">المدفوع</th>
                        <th class="number">المتبقي</th>
                        <th class="number">نسبة الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_client'] as $client)
                        <tr>
                            <td>{{ $client['client_name'] ?? '' }}</td>
                            <td class="number">{{ $client['invoices_count'] ?? 0 }}</td>
                            <td class="number">{{ number_format($client['total_amount'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($client['paid_amount'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($client['remaining_amount'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format((($client['total_amount'] ?? 0) > 0 ? (($client['paid_amount'] ?? 0) / ($client['total_amount'] ?? 0) * 100) : 0), 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Sales by Category -->
    @if(isset($data['by_category']) && count($data['by_category']) > 0)
        <div class="section">
            <div class="section-title">المبيعات حسب الفئة</div>
            <table>
                <thead>
                    <tr>
                        <th>الفئة</th>
                        <th class="number">الكمية</th>
                        <th class="number">الإيرادات</th>
                        <th class="number">التكلفة</th>
                        <th class="number">الربح</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_category'] as $category)
                        <tr>
                            <td>{{ $category['category_name'] ?? '' }}</td>
                            <td class="number">{{ number_format($category['quantity'] ?? 0, 2) }}</td>
                            <td class="number">{{ number_format($category['revenue'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($category['cost'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($category['profit'] ?? 0, 2) }} ج.م</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
