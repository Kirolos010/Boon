<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الأرباح</title>
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

        .text-left {
            text-align: left;
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

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            font-weight: bold;
            text-align: right;
        }

        .summary-value {
            text-align: left;
            font-weight: bold;
            color: #2C1810;
        }

        .total-box {
            background-color: #D4AF37;
            color: #2C1810;
            padding: 15px;
            text-align: center;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير الأرباح</h1>
        <p>تحليل الأرباح والتكاليف والمصروفات</p>
    </div>

    @if(isset($data['date_range']))
        <div class="date-range">
            من {{ $data['date_range']['from'] }} إلى {{ $data['date_range']['to'] }}
        </div>
    @endif

    <!-- Summary Section -->
    <div class="section">
        <div class="section-title">الملخص المالي</div>
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">إجمالي الإيرادات:</span>
                <span class="summary-value">{{ number_format($data['revenue_total'] ?? 0, 2) }} ج.م</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">إجمالي التكاليف:</span>
                <span class="summary-value">{{ number_format($data['total_cost'] ?? 0, 2) }} ج.م</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">إجمالي الأرباح (قبل المصروفات):</span>
                <span class="summary-value">{{ number_format($data['total_profit'] ?? 0, 2) }} ج.م</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">إجمالي المصروفات:</span>
                <span class="summary-value">{{ number_format($data['expenses_total'] ?? 0, 2) }} ج.م</span>
            </div>
            <div class="summary-row" style="background-color: #fff; margin-top: 10px; padding-top: 10px; border-top: 2px solid #2C1810;">
                <span class="summary-label" style="font-size: 14px;">صافي الربح النهائي:</span>
                <span class="summary-value" style="font-size: 14px; color: #27ae60;">{{ number_format(($data['net_profit'] ?? 0), 2) }} ج.م</span>
            </div>
        </div>
    </div>

    <!-- Sales Breakdown -->
    @if(isset($data['sales_breakdown']) && count($data['sales_breakdown']) > 0)
        <div class="section">
            <div class="section-title">تفصيل المبيعات</div>
            <table>
                <thead>
                    <tr>
                        <th>نوع المبيعات</th>
                        <th class="number">الإيرادات</th>
                        <th class="number">التكلفة</th>
                        <th class="number">الربح</th>
                        <th class="number">نسبة الربح</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['sales_breakdown'] as $sale)
                        <tr>
                            <td>{{ $sale['type'] === 'invoices' ? 'فواتير عادية' : 'مبيعات سريعة' }}</td>
                            <td class="number">{{ number_format($sale['revenue'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($sale['cost'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format($sale['profit'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format((($sale['revenue'] ?? 0) > 0 ? (($sale['profit'] ?? 0) / ($sale['revenue'] ?? 0) * 100) : 0), 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Expenses by Category -->
    @if(isset($data['expenses_by_category']) && count($data['expenses_by_category']) > 0)
        <div class="section">
            <div class="section-title">المصروفات حسب الفئة</div>
            <table>
                <thead>
                    <tr>
                        <th>فئة المصروف</th>
                        <th class="number">المبلغ</th>
                        <th class="number">نسبة من الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['expenses_by_category'] as $expense)
                        <tr>
                            <td>{{ $expense['category_name'] ?? $expense['category'] ?? '' }}</td>
                            <td class="number">{{ number_format($expense['total'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format((($expense['total'] ?? 0) / (($data['expenses_total'] ?? 1)) * 100), 1) }}%</td>
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
