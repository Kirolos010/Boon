<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الإغلاق اليومي</title>
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

        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-item {
            background-color: #f5e6d3;
            padding: 15px;
            text-align: center;
            border-radius: 4px;
            border-left: 4px solid #D4AF37;
        }

        .summary-item label {
            font-size: 11px;
            color: #666;
            display: block;
            margin-bottom: 8px;
        }

        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #2C1810;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #6F4E37;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            border-radius: 4px;
        }

        .sales-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .card {
            background-color: #f5e6d3;
            padding: 12px;
            border-radius: 4px;
            border-left: 4px solid #D4AF37;
        }

        .card-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        .card-row:last-child {
            border-bottom: none;
        }

        .card-label {
            color: #666;
        }

        .card-value {
            font-weight: bold;
            color: #2C1810;
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
            font-size: 12px;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .number {
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #2C1810;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        .total-box {
            background: linear-gradient(135deg, #D4AF37 0%, #f5d86e 100%);
            color: #2C1810;
            padding: 15px;
            text-align: center;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }

        .payment-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .payment-item {
            background-color: #f5e6d3;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }

        .payment-item label {
            font-size: 10px;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }

        .payment-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #2C1810;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير الإغلاق اليومي</h1>
        <p>ملخص يومي شامل للمبيعات والمصروفات والتدفق النقدي</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary">
        <div class="summary-item">
            <label>إجمالي الإيرادات</label>
            <div class="value">{{ number_format($data['revenue_total'] ?? 0, 2) }} ج.م</div>
        </div>
        <div class="summary-item">
            <label>إجمالي الأرباح</label>
            <div class="value">{{ number_format($data['profit_total'] ?? 0, 2) }} ج.م</div>
        </div>
        <div class="summary-item">
            <label>إجمالي المصروفات</label>
            <div class="value">{{ number_format($data['expenses_total'] ?? 0, 2) }} ج.م</div>
        </div>
        <div class="summary-item">
            <label>صافي الربح</label>
            <div class="value" style="color: #27ae60;">{{ number_format($data['net_profit'] ?? 0, 2) }} ج.م</div>
        </div>
    </div>

    <!-- Sales Breakdown -->
    <div class="section">
        <div class="section-title">تفصيل المبيعات</div>
        <div class="sales-cards">
            <div class="card">
                <div style="font-weight: bold; margin-bottom: 8px; color: #2C1810;">الفواتير العادية</div>
                <div class="card-row">
                    <span class="card-label">عدد الفواتير:</span>
                    <span class="card-value">{{ $data['invoices_count'] ?? 0 }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">الإيرادات:</span>
                    <span class="card-value">{{ number_format($data['invoices_revenue'] ?? 0, 2) }} ج.م</span>
                </div>
                <div class="card-row">
                    <span class="card-label">التكلفة:</span>
                    <span class="card-value">{{ number_format($data['invoices_cost'] ?? 0, 2) }} ج.م</span>
                </div>
                <div class="card-row" style="border-bottom: 2px solid #2C1810; padding-bottom: 5px;">
                    <span class="card-label">الربح:</span>
                    <span class="card-value" style="color: #27ae60;">{{ number_format($data['invoices_profit'] ?? 0, 2) }} ج.م</span>
                </div>
            </div>

            <div class="card">
                <div style="font-weight: bold; margin-bottom: 8px; color: #2C1810;">المبيعات السريعة</div>
                <div class="card-row">
                    <span class="card-label">عدد العمليات:</span>
                    <span class="card-value">{{ $data['quick_sales_count'] ?? 0 }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">الإيرادات:</span>
                    <span class="card-value">{{ number_format($data['quick_sales_revenue'] ?? 0, 2) }} ج.م</span>
                </div>
                <div class="card-row">
                    <span class="card-label">التكلفة:</span>
                    <span class="card-value">{{ number_format($data['quick_sales_cost'] ?? 0, 2) }} ج.م</span>
                </div>
                <div class="card-row" style="border-bottom: 2px solid #2C1810; padding-bottom: 5px;">
                    <span class="card-label">الربح:</span>
                    <span class="card-value" style="color: #27ae60;">{{ number_format($data['quick_sales_profit'] ?? 0, 2) }} ج.م</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Breakdown -->
    @if(isset($data['expenses_by_category']) && count($data['expenses_by_category']) > 0)
        <div class="section">
            <div class="section-title">تفاصيل المصروفات</div>
            <table>
                <thead>
                    <tr>
                        <th>الفئة</th>
                        <th class="number">المبلغ</th>
                        <th class="number">النسبة من الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['expenses_by_category'] as $category)
                        <tr>
                            <td>{{ $category['category_name'] ?? $category['category'] ?? '' }}</td>
                            <td class="number">{{ number_format($category['total'] ?? 0, 2) }} ج.م</td>
                            <td class="number">{{ number_format((($category['total'] ?? 0) / (($data['expenses_total'] ?? 1)) * 100), 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Cash Flow Summary -->
    <div class="section">
        <div class="section-title">ملخص التدفق النقدي</div>
        <table>
            <tr>
                <td style="font-weight: bold;">إجمالي الإيرادات</td>
                <td class="number" style="font-weight: bold; color: #27ae60;">+ {{ number_format($data['revenue_total'] ?? 0, 2) }} ج.م</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">التكلفة الإجمالية</td>
                <td class="number" style="font-weight: bold; color: #e74c3c;">- {{ number_format($data['total_cost'] ?? 0, 2) }} ج.م</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">المصروفات</td>
                <td class="number" style="font-weight: bold; color: #e74c3c;">- {{ number_format($data['expenses_total'] ?? 0, 2) }} ج.م</td>
            </tr>
        </table>
        <div class="total-box">
            صافي الربح النهائي: {{ number_format($data['net_profit'] ?? 0, 2) }} ج.م
        </div>
    </div>

    <!-- Payment Status -->
    @if(isset($data['payment_status']))
        <div class="section">
            <div class="section-title">حالة الدفع</div>
            <div class="payment-summary">
                <div class="payment-item">
                    <label>المدفوع نقداً</label>
                    <div class="value">{{ number_format($data['payment_status']['cash'] ?? 0, 2) }} ج.م</div>
                </div>
                <div class="payment-item">
                    <label>الشيكات</label>
                    <div class="value">{{ number_format($data['payment_status']['check'] ?? 0, 2) }} ج.م</div>
                </div>
                <div class="payment-item">
                    <label>على الحساب</label>
                    <div class="value">{{ number_format($data['payment_status']['credit'] ?? 0, 2) }} ج.م</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Operations Summary -->
    <div class="section">
        <div class="section-title">ملخص العمليات</div>
        <table>
            <tr>
                <td>العملاء المخدومين</td>
                <td class="number">{{ $data['total_clients_served'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>إجمالي المنتجات المباعة</td>
                <td class="number">{{ number_format($data['total_quantity_sold'] ?? 0, 2) }} كج</td>
            </tr>
            <tr>
                <td>معدل الربح</td>
                <td class="number">{{ number_format(($data['revenue_total'] > 0 ? ($data['profit_total'] / $data['revenue_total'] * 100) : 0), 1) }}%</td>
            </tr>
            <tr>
                <td>متوسط سعر الفاتورة</td>
                <td class="number">{{ number_format(($data['invoices_count'] > 0 ? ($data['invoices_revenue'] / $data['invoices_count']) : 0), 2) }} ج.م</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
