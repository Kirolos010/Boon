@extends('layouts.app')

@section('title', 'تقرير الإغلاق اليومي')
@section('navbar-title', 'تقرير الإغلاق اليومي')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">تقارير</li>
            <li class="breadcrumb-item active">الإغلاق اليومي</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تقرير الإغلاق اليومي</h1>
            <p class="page-title-subtitle">ملخص يومي شامل للمبيعات والمصروفات والتدفق النقدي</p>
        </div>
        {{-- <div class="page-actions">
            <form method="POST" action="{{ route('reports.export-pdf', 'daily-closing') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> تنزيل PDF
                </button>
            </form>
            <form method="POST" action="{{ route('reports.export-excel', 'daily-closing') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> تنزيل Excel
                </button>
            </form>
        </div> --}}
    </div>

    <!-- Main Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-arrow-up"
                label="إجمالي الإيرادات"
                value="{{ number_format($dailyClosing['revenue_total'], 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-coins"
                label="إجمالي الأرباح"
                value="{{ number_format($dailyClosing['profit_total'], 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-receipt"
                label="إجمالي المصروفات"
                value="{{ number_format($dailyClosing['expenses_total'], 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-cash-register"
                label="صافي الربح"
                value="{{ number_format($dailyClosing['net_profit'], 2) }}"
                change="ج.م" />
        </div>
    </div>

    <!-- Sales Breakdown Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <x-card>
                @slot('header')
                    <i class="fas fa-receipt"></i> فواتير عادية
                @endslot
                <div style="padding: 20px;">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">عدد الفواتير</small>
                            <p style="font-size: 24px; font-weight: bold; color: var(--coffee-dark);">{{ $dailyClosing['invoices_count'] ?? 0 }}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">الإيرادات</small>
                            <p style="font-size: 24px; font-weight: bold; color: #27ae60;">{{ number_format($dailyClosing['invoices_revenue'], 2) }} ج.م</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">التكلفة</small>
                            <p style="font-weight: bold;">{{ number_format($dailyClosing['invoices_cost'], 2) }} ج.م</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">الربح</small>
                            <p style="font-weight: bold; color: #27ae60;">{{ number_format($dailyClosing['invoices_profit'], 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-6">
            <x-card>
                @slot('header')
                    <i class="fas fa-bolt"></i> مبيعات سريعة
                @endslot
                <div style="padding: 20px;">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">عدد العمليات</small>
                            <p style="font-size: 24px; font-weight: bold; color: var(--coffee-dark);">{{ $dailyClosing['quick_sales_count'] ?? 0 }}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">الإيرادات</small>
                            <p style="font-size: 24px; font-weight: bold; color: #27ae60;">{{ number_format($dailyClosing['quick_sales_revenue'], 2) }} ج.م</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">التكلفة</small>
                            <p style="font-weight: bold;">{{ number_format($dailyClosing['quick_sales_cost'], 2) }} ج.م</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">الربح</small>
                            <p style="font-weight: bold; color: #27ae60;">{{ number_format($dailyClosing['quick_sales_profit'], 2) }} ج.م</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Expenses Breakdown -->
    <x-card class="mb-4">
        @slot('header')
            <i class="fas fa-receipt"></i> تفاصيل المصروفات
        @endslot

        @if(isset($dailyClosing['expenses_by_category']) && count($dailyClosing['expenses_by_category']) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead style="background: var(--cream-light);">
                        <tr>
                            <th>الفئة</th>
                            <th class="text-end">المبلغ</th>
                            <th class="text-end">نسبة من الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyClosing['expenses_by_category'] as $category)
                            <tr>
                                <td>{{ $category['category_name'] ?? $category['category'] }}</td>
                                <td class="text-end" style="font-weight: bold;">{{ number_format($category['total'], 2) }} ج.م</td>
                                <td class="text-end">
                                    <span class="badge badge-info">
                                        {{ number_format(($category['total'] / ($dailyClosing['expenses_total'] ?: 1) * 100), 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center" style="padding: 20px;">لا توجد مصروفات</p>
        @endif
    </x-card>

    <!-- Cash Flow Summary -->
    <div class="row">
        <div class="col-lg-8">
            <x-card>
                @slot('header')
                    <i class="fas fa-chart-pie"></i> ملخص التدفق النقدي
                @endslot

                <div style="padding: 20px;">
                    <div class="mb-3" style="border-bottom: 1px solid #e0e0e0; padding-bottom: 15px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-weight: bold;">إجمالي الإيرادات:</span>
                            <span style="font-size: 18px; color: #27ae60; font-weight: bold;">
                                {{ number_format($dailyClosing['revenue_total'], 2) }} ج.م
                            </span>
                        </div>
                    </div>

                    <div class="mb-3" style="border-bottom: 1px solid #e0e0e0; padding-bottom: 15px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>التكلفة الإجمالية:</span>
                            <span style="color: #e74c3c;">- {{ number_format($dailyClosing['total_cost'], 2) }} ج.م</span>
                        </div>
                    </div>

                    <div class="mb-3" style="border-bottom: 1px solid #e0e0e0; padding-bottom: 15px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>المصروفات:</span>
                            <span style="color: #e74c3c;">- {{ number_format($dailyClosing['expenses_total'], 2) }} ج.م</span>
                        </div>
                    </div>

                    <div style="padding: 15px; background: var(--cream-light); border-radius: 8px; text-align: center;">
                        <small class="text-muted d-block mb-2">صافي الربح النهائي</small>
                        <p style="font-size: 28px; font-weight: bold; color: var(--coffee-dark); margin: 0;">
                            {{ number_format($dailyClosing['net_profit'], 2) }} ج.م
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card>
                @slot('header')
                    <i class="fas fa-info-circle"></i> ملخص العمليات
                @endslot

                <div style="padding: 20px;">
                    <div style="padding: 12px; background: var(--cream-light); border-radius: 6px; margin-bottom: 12px;">
                        <small class="text-muted d-block">العملاء المخدومين</small>
                        <p style="font-size: 20px; font-weight: bold; color: var(--coffee-dark); margin: 0;">
                            {{ $dailyClosing['total_clients_served'] ?? 0 }}
                        </p>
                    </div>

                    <div style="padding: 12px; background: var(--cream-light); border-radius: 6px; margin-bottom: 12px;">
                        <small class="text-muted d-block">إجمالي المنتجات المباعة</small>
                        <p style="font-size: 20px; font-weight: bold; color: var(--coffee-dark); margin: 0;">
                            {{ number_format($dailyClosing['total_quantity_sold'], 2) }} كج
                        </p>
                    </div>

                    <div style="padding: 12px; background: var(--cream-light); border-radius: 6px; margin-bottom: 12px;">
                        <small class="text-muted d-block">معدل الربح</small>
                        <p style="font-size: 20px; font-weight: bold; color: #27ae60; margin: 0;">
                            {{ number_format(($dailyClosing['revenue_total'] > 0 ? ($dailyClosing['profit_total'] / $dailyClosing['revenue_total'] * 100) : 0), 1) }}%
                        </p>
                    </div>

                    <div style="padding: 12px; background: var(--cream-light); border-radius: 6px;">
                        <small class="text-muted d-block">متوسط سعر العملية</small>
                        <p style="font-size: 20px; font-weight: bold; color: var(--coffee-dark); margin: 0;">
                            {{ number_format(($dailyClosing['invoices_count'] > 0 ? ($dailyClosing['invoices_revenue'] / $dailyClosing['invoices_count']) : 0), 2) }} ج.م
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Payment Status -->
    @if(isset($dailyClosing['payment_status']))
        <x-card class="mt-4">
            @slot('header')
                <i class="fas fa-credit-card"></i> حالة الدفع
            @endslot

            <div class="row" style="padding: 20px;">
                <div class="col-md-4 text-center">
                    <div style="padding: 15px;">
                        <small class="text-muted d-block mb-2">المدفوع نقداً</small>
                        <p style="font-size: 22px; font-weight: bold; color: #27ae60;">
                            {{ number_format($dailyClosing['payment_status']['cash'] ?? 0, 2) }} ج.م
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="padding: 15px;">
                        <small class="text-muted d-block mb-2">الشيكات</small>
                        <p style="font-size: 22px; font-weight: bold; color: #3498db;">
                            {{ number_format($dailyClosing['payment_status']['check'] ?? 0, 2) }} ج.م
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="padding: 15px;">
                        <small class="text-muted d-block mb-2">على الحساب</small>
                        <p style="font-size: 22px; font-weight: bold; color: #f39c12;">
                            {{ number_format($dailyClosing['payment_status']['credit'] ?? 0, 2) }} ج.م
                        </p>
                    </div>
                </div>
            </div>
        </x-card>
    @endif
@endsection
