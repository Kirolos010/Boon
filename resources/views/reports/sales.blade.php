@extends('layouts.app')

@section('title', 'تقرير المبيعات')
@section('navbar-title', 'تقرير المبيعات')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">تقارير</li>
            <li class="breadcrumb-item active">المبيعات</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تقرير المبيعات</h1>
            <p class="page-title-subtitle">تحليل مفصل لمبيعات المنتجات والعملاء والفئات</p>
        </div>
        <div class="page-actions">
            <form method="POST" action="{{ route('reports.export-pdf', 'sales') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> تنزيل PDF
                </button>
            </form>
            <form method="POST" action="{{ route('reports.export-excel', 'sales') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> تنزيل Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">من التاريخ</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">إلى التاريخ</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
                </div>
                <div class="col-md-2" style="padding-top: 32px;">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales by Product -->
    <x-card>
        @slot('header')
            <i class="fas fa-cube"></i> المبيعات حسب المنتج
        @endslot

        @if($salesByProduct->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr style="background: var(--cream-light);">
                            <th>المنتج</th>
                            <th class="text-end">الكمية (كج)</th>
                            <th class="text-end">متوسط السعر</th>
                            <th class="text-end">الإيراد</th>
                            <th class="text-end">التكلفة</th>
                            <th class="text-end">الربح</th>
                            <th class="text-end">نسبة الربح</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByProduct as $item)
                            <tr>
                                <td><strong>{{ $item['name_ar'] ?? $item['name'] }}</strong></td>
                                <td class="text-end">{{ number_format($item['total_quantity_kg'], 2) }}</td>
                                <td class="text-end">{{ number_format($item['average_price'], 2) }} ر.س</td>
                                <td class="text-end">{{ number_format($item['total_revenue'], 2) }} ر.س</td>
                                <td class="text-end">{{ number_format($item['total_cost'], 2) }} ر.س</td>
                                <td class="text-end" style="color: #28a745; font-weight: bold;">{{ number_format($item['profit'], 2) }} ر.س</td>
                                <td class="text-end">
                                    <span class="badge badge-success">{{ number_format($item['profit_margin'], 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid var(--cream-medium); background: var(--cream-light); font-weight: bold;">
                            <td colspan="2">الإجمالي</td>
                            <td class="text-end"></td>
                            <td class="text-end">{{ number_format($salesByProduct->sum('total_revenue'), 2) }} ر.س</td>
                            <td class="text-end">{{ number_format($salesByProduct->sum('total_cost'), 2) }} ر.س</td>
                            <td class="text-end" style="color: #28a745;">{{ number_format($salesByProduct->sum('profit'), 2) }} ر.س</td>
                            <td class="text-end">{{ number_format(($salesByProduct->sum('profit') / $salesByProduct->sum('total_revenue') * 100), 1) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center" style="padding: 30px;">لا توجد بيانات بيع</p>
        @endif
    </x-card>

    <!-- Sales by Client -->
    <x-card class="mt-4">
        @slot('header')
            <i class="fas fa-user-tie"></i> المبيعات حسب العميل
        @endslot

        @if($salesByClient->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr style="background: var(--cream-light);">
                            <th>العميل</th>
                            <th class="text-end">عدد الفواتير</th>
                            <th class="text-end">إجمالي المبيعات</th>
                            <th class="text-end">المدفوع</th>
                            <th class="text-end">المعلق</th>
                            <th class="text-end">نسبة الدفع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByClient as $item)
                            <tr>
                                <td><strong>{{ $item['name'] }}</strong></td>
                                <td class="text-end">{{ $item['total_invoices'] }}</td>
                                <td class="text-end">{{ number_format($item['total_amount'], 2) }} ر.س</td>
                                <td class="text-end" style="color: #28a745;">{{ number_format($item['total_paid'], 2) }} ر.س</td>
                                <td class="text-end" style="color: #ffc107;">{{ number_format($item['total_pending'], 2) }} ر.س</td>
                                <td class="text-end">
                                    <span class="badge badge-info">{{ number_format($item['payment_rate'], 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center" style="padding: 30px;">لا توجد بيانات عملاء</p>
        @endif
    </x-card>

    <!-- Sales by Category -->
    <x-card class="mt-4">
        @slot('header')
            <i class="fas fa-list"></i> المبيعات حسب الفئة
        @endslot

        @if($salesByCategory->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr style="background: var(--cream-light);">
                            <th>الفئة</th>
                            <th class="text-end">الكمية (كج)</th>
                            <th class="text-end">الإيراد</th>
                            <th class="text-end">التكلفة</th>
                            <th class="text-end">الربح</th>
                            <th class="text-end">نسبة الربح</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByCategory as $item)
                            <tr>
                                <td><strong>{{ $item['name_ar'] ?? $item['name'] }}</strong></td>
                                <td class="text-end">{{ number_format($item['total_quantity_kg'], 2) }}</td>
                                <td class="text-end">{{ number_format($item['total_revenue'], 2) }} ر.س</td>
                                <td class="text-end">{{ number_format($item['total_cost'], 2) }} ر.س</td>
                                <td class="text-end" style="color: #28a745;">{{ number_format($item['profit'], 2) }} ر.س</td>
                                <td class="text-end">
                                    <span class="badge badge-success">{{ number_format($item['profit_margin'], 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center" style="padding: 30px;">لا توجد بيانات فئة</p>
        @endif
    </x-card>
@endsection
