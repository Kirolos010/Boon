@extends('layouts.app')

@section('title', 'تقرير الأرباح')
@section('navbar-title', 'تقرير الأرباح')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">تقارير</li>
            <li class="breadcrumb-item active">الأرباح</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تقرير الأرباح</h1>
            <p class="page-title-subtitle">تحليل الأرباح والخسائر والمصروفات</p>
        </div>
        {{-- <div class="page-actions">
            <form method="POST" action="{{ route('reports.export-pdf', 'profit') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> تنزيل PDF
                </button>
            </form>
            <form method="POST" action="{{ route('reports.export-excel', 'profit') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> تنزيل Excel
                </button>
            </form>
        </div> --}}
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit') }}" class="row g-3">
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

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-dollar-sign"
                label="إجمالي الإيراد"
                value="{{ number_format($profitReport['sales_data']['total_revenue'] ?? 0, 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-minus-circle"
                label="إجمالي التكاليف"
                value="{{ number_format($profitReport['sales_data']['total_cost'] ?? 0, 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-chart-line"
                label="الربح الإجمالي"
                value="{{ number_format($profitReport['net_data']['gross_profit'] ?? 0, 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-check-circle"
                label="الربح الصافي"
                value="{{ number_format($profitReport['net_data']['net_profit'] ?? 0, 2) }}"
                change="ج.م" />
        </div>
    </div>

    <!-- Sales Breakdown -->
    <x-card>
        @slot('header')
            <i class="fas fa-shopping-cart"></i> ملخص المبيعات
        @endslot

        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3">الفواتير المنتظمة</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>عدد الفواتير:</td>
                        <td class="text-end">{{ $profitReport['sales_data']['regular_invoices_count'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>الإيراد:</td>
                        <td class="text-end">{{ number_format($profitReport['sales_data']['regular_revenue'] ?? 0, 2) }} ج.م</td>
                    </tr>
                    <tr>
                        <td>التكلفة:</td>
                        <td class="text-end">{{ number_format($profitReport['sales_data']['regular_cost'] ?? 0, 2) }} ج.م</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="mb-3">البيع السريع</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>عدد العمليات:</td>
                        <td class="text-end">{{ $profitReport['sales_data']['quick_sales_count'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>الإيراد:</td>
                        <td class="text-end">{{ number_format($profitReport['sales_data']['quick_revenue'] ?? 0, 2) }} ج.م</td>
                    </tr>
                    <tr>
                        <td>التكلفة:</td>
                        <td class="text-end">{{ number_format($profitReport['sales_data']['quick_cost'] ?? 0, 2) }} ج.م</td>
                    </tr>
                </table>
            </div>
        </div>
    </x-card>

    <!-- Expenses by Category -->
    <x-card class="mt-4">
        @slot('header')
            <i class="fas fa-money-bill-wave"></i> النفقات حسب الفئة
        @endslot

        @if(!empty($profitReport['expenses_data']))
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                         <tr style="background: var(--cream-light);">
                            <th>فئة النفقة</th>
                            <th class="text-end">المبلغ</th>
                            <th class="text-end">النسبة المئوية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitReport['expenses_data'] as $expense)
                            <tr>
                                <td><strong>{{ $expense['category_name_ar'] ?? $expense['category_name'] }}</strong></td>
                                <td class="text-end">{{ number_format($expense['amount'], 2) }} ج.م</td>
                                <td class="text-end">
                                    <span class="badge badge-warning">{{ number_format($expense['percentage'], 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid var(--cream-medium); background: var(--cream-light); font-weight: bold;">
                            <td>الإجمالي</td>
                            <td class="text-end">{{ number_format(collect($profitReport['expenses_data'])->sum('amount'), 2) }} ج.م</td>
                            <td class="text-end">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center" style="padding: 30px;">لا توجد نفقات في هذه الفترة</p>
        @endif
    </x-card>

    <!-- Net Profit Calculation -->
    <x-card class="mt-4">
        @slot('header')
            <i class="fas fa-calculator"></i> حساب الربح الصافي
        @endslot

        <table class="table table-borderless">
            <tr style="border-bottom: 2px solid var(--cream-light);">
                <td style="font-weight: bold;">الربح الإجمالي</td>
                <td class="text-end" style="font-weight: bold;">{{ number_format($profitReport['net_data']['gross_profit'] ?? 0, 2) }} ج.م</td>
            </tr>
            <tr style="border-bottom: 2px solid var(--cream-light);">
                <td style="font-weight: bold;">ناقص: إجمالي النفقات</td>
                <td class="text-end" style="font-weight: bold;">- {{ number_format($profitReport['net_data']['total_expenses'] ?? 0, 2) }} ج.م</td>
            </tr>
            <tr style="background: var(--cream-light); font-weight: bold; font-size: 18px;">
                <td>الربح الصافي</td>
                <td class="text-end" style="color: #28a745;">{{ number_format($profitReport['net_data']['net_profit'] ?? 0, 2) }} ج.م</td>
            </tr>
        </table>
    </x-card>
@endsection
