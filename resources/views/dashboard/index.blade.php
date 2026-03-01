@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('navbar-title', 'لوحة التحكم')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">أهلاً وسهلاً، {{ auth()->user()->name }}</h1>
            <p class="page-title-subtitle">إليك ملخص أنشطة اليوم وأداء الأعمال</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> فاتورة جديدة
            </a>
            <a href="{{ route('quick-sales.create') }}" class="btn btn-success">
                <i class="fas fa-bolt"></i> بيع سريع
            </a>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-file-invoice-dollar"
                label="إجمالي الفواتير"
                value="{{ $stats['total_invoices'] ?? 0 }}"
                change="+12% من الشهر الماضي"
                changeClass="positive" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-bolt"
                label="البيع السريع"
                value="{{ $stats['quick_sales'] ?? 0 }}"
                change="+5% من الأمس"
                changeClass="positive" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-users"
                label="عدد العملاء"
                value="{{ $stats['total_clients'] ?? 0 }}"
                change="لا توجد تغييرات"
                changeClass="positive" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-cube"
                label="المنتجات"
                value="{{ $stats['total_products'] ?? 0 }}"
                change="{{ $stats['total_products'] ?? 0 }} منتج متاح"
                changeClass="positive" />
        </div>
    </div>

    <!-- Today's Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <x-card>
                @slot('header')
                    <i class="fas fa-chart-line"></i> ملخص اليوم
                @endslot

                @if($today_summary ?? false)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>إجمالي الفواتير:</strong></td>
                            <td class="text-end">{{ $today_summary['total_invoices'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td><strong>إجمالي المبيعات:</strong></td>
                            <td class="text-end">{{ number_format($today_summary['total_sales'] ?? 0, 2) }} ج.م</td>
                        </tr>
                        <tr>
                            <td><strong>إجمالي الأرباح:</strong></td>
                            <td class="text-end" style="color: #28a745;">{{ number_format($today_summary['total_profit'] ?? 0, 2) }} ج.م</td>
                        </tr>
                        <tr>
                            <td><strong>المبالغ المدفوعة:</strong></td>
                            <td class="text-end">{{ number_format($today_summary['total_paid'] ?? 0, 2) }} ج.م</td>
                        </tr>
                        <tr style="border-top: 2px solid var(--cream-medium);">
                            <td><strong>المبالغ المعلقة:</strong></td>
                            <td class="text-end" style="color: #ffc107;">{{ number_format($today_summary['total_pending'] ?? 0, 2) }} ج.م</td>
                        </tr>
                    </table>
                @endif
            </x-card>
        </div>

        <div class="col-md-6">
            <x-card>
                @slot('header')
                    <i class="fas fa-exclamation-triangle"></i> الحالات التحذيرية
                @endslot

                @if($low_stock_products->count() > 0)
                    <div style="max-height: 300px; overflow-y: auto;">
                        @foreach($low_stock_products->take(5) as $item)
                            <div style="padding: 12px 0; border-bottom: 1px solid var(--cream-light); display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong>{{ $item['product']->name_ar ?? $item['product']->name }}</strong><br>
                                    <small class="text-muted">الكمية الحالية: {{ $item['current_stock'] }} كج</small>
                                </div>
                                <span class="badge badge-warning">{{ number_format($item['shortage'], 2) }} كج ناقص</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center" style="padding: 20px;">
                        <i class="fas fa-check-circle" style="font-size: 30px; color: #28a745;"></i><br>
                        جميع المنتجات بمستويات جيدة
                    </p>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="row g-3">
        <div class="col-md-6">
            <x-card>
                @slot('header')
                    <i class="fas fa-shopping-cart"></i> أكثر المنتجات مبيعاً
                @endslot

                @if($top_products->count() > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr style="background: var(--cream-light);">
                                <th>المنتج</th>
                                <th class="text-end">المبيعات</th>
                                <th class="text-end">الإيراد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($top_products->take(5) as $product)
                                <tr>
                                    <td>{{ $product['product_name_ar'] ?? $product['product_name'] }}</td>
                                    <td class="text-end">{{ number_format($product['total_quantity_kg'], 2) }} كج</td>
                                    <td class="text-end">{{ number_format($product['total_revenue'], 2) }} ج.م</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </x-card>
        </div>

        <div class="col-md-6">
            <x-card>
                @slot('header')
                    <i class="fas fa-user-tie"></i> أكثر العملاء شراءً
                @endslot

                @if($top_clients->count() > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr style="background: var(--cream-light);">
                                <th>العميل</th>
                                <th class="text-end">المشتريات</th>
                                <th class="text-end">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($top_clients->take(5) as $client)
                                <tr>
                                    <td>{{ $client['client_name_ar'] ?? $client['client_name'] }}</td>
                                    <td class="text-end">{{ $client['total_invoices'] }}</td>
                                    <td class="text-end">{{ number_format($client['total_amount'], 2) }} ج.م</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($stats['pending_payments'] ?? 0 > 0)
        <div class="mt-4">
            <x-alert message="هناك {{ $stats['pending_payments'] }} فاتورة معلقة تحتاج إلى متابعة الدفع" type="warning" icon="exclamation-circle" />
        </div>
    @endif

    @if($stats['pending_purchases'] ?? 0 > 0)
        <div class="mt-2">
            <x-alert message="هناك {{ $stats['pending_purchases'] }} طلب شراء منتظر الاستلام" type="info" icon="info-circle" />
        </div>
    @endif
@endsection
