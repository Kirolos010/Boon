<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المخزون</title>
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
            font-size: 10px;
        }

        .header {
            background-color: #2C1810;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            opacity: 0.9;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-item {
            background-color: #f5e6d3;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            border-left: 3px solid #D4AF37;
        }

        .summary-item label {
            font-size: 9px;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #2C1810;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            background-color: #6F4E37;
            color: white;
            padding: 8px;
            margin-bottom: 8px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }

        th {
            background-color: #f5e6d3;
            padding: 6px;
            text-align: right;
            border-bottom: 2px solid #2C1810;
            font-weight: bold;
            font-size: 9px;
        }

        td {
            padding: 5px 6px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .number {
            text-align: left;
        }

        .status-ok {
            color: #27ae60;
            font-weight: bold;
        }

        .status-low {
            color: #f39c12;
            font-weight: bold;
        }

        .status-out {
            color: #e74c3c;
            font-weight: bold;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #2C1810;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير المخزون</h1>
        <p>حالة المخزون الحالية وتقييمه</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-item">
            <label>إجمالي المنتجات</label>
            <div class="value">{{ $data['summary']['total_products'] ?? 0 }}</div>
        </div>
        <div class="summary-item">
            <label>إجمالي الكمية (كج)</label>
            <div class="value">{{ number_format($data['summary']['total_quantity_kg'] ?? 0, 2) }}</div>
        </div>
        <div class="summary-item">
            <label>قيمة المخزون</label>
            <div class="value">{{ number_format($data['summary']['total_value'] ?? 0, 2) }} ج.م</div>
        </div>
        <div class="summary-item">
            <label>منتجات حد أدنى</label>
            <div class="value">{{ $data['summary']['low_stock_count'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Inventory Details -->
    @if(isset($data['products']) && count($data['products']) > 0)
        <div class="section">
            <div class="section-title">تفاصيل المخزون</div>
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الفئة</th>
                        <th class="number">الكمية (كج)</th>
                        <th class="number">سعر الشراء</th>
                        <th class="number">قيمة المخزون</th>
                        <th class="number">سعر البيع</th>
                        <th class="number">الربح %</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['products'] as $product)
                        <tr>
                            <td>{{ $product['name_ar'] ?? $product['name'] ?? '' }}</td>
                            <td>{{ $product['category'] ?? '-' }}</td>
                            <td class="number">{{ number_format($product['current_stock_kg'] ?? 0, 2) }}</td>
                            <td class="number">{{ number_format($product['purchase_price_per_kg'] ?? 0, 2) }}</td>
                            <td class="number">{{ number_format($product['stock_value'] ?? 0, 2) }}</td>
                            <td class="number">{{ number_format($product['selling_price_per_kg'] ?? 0, 2) }}</td>
                            <td class="number">{{ number_format($product['profit_margin'] ?? 0, 1) }}%</td>
                            <td>
                                @if($product['status'] === 'ok')
                                    <span class="status-ok">متوفر</span>
                                @elseif($product['status'] === 'low')
                                    <span class="status-low">حد أدنى</span>
                                @else
                                    <span class="status-out">نفد</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Low Stock Alert -->
    @php
        $lowStockProducts = isset($data['products'])
            ? collect($data['products'])->filter(fn($p) => $p['status'] === 'low' || $p['status'] === 'out')
            : collect([]);
    @endphp

    @if($lowStockProducts->count() > 0)
        <div class="section">
            <div class="section-title">⚠️ المنتجات تحت الحد الأدنى</div>
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th class="number">الكمية الحالية</th>
                        <th class="number">الحد الأدنى</th>
                        <th class="number">الكمية الناقصة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                        <tr>
                            <td>{{ $product['name_ar'] ?? $product['name'] ?? '' }}</td>
                            <td class="number">{{ number_format($product['current_stock_kg'] ?? 0, 2) }} كج</td>
                            <td class="number">{{ number_format($product['minimum_stock_alert'] ?? 0, 2) }} كج</td>
                            <td class="number" style="color: #e74c3c; font-weight: bold;">{{ number_format($product['shortage'] ?? 0, 2) }} كج</td>
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
