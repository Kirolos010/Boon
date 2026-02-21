@extends('layouts.app')

@section('title', 'تقرير المخزون')
@section('navbar-title', 'تقرير المخزون')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">تقارير</li>
            <li class="breadcrumb-item active">المخزون</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تقرير المخزون</h1>
            <p class="page-title-subtitle">حالة المخزون الحالية وتقييمه</p>
        </div>
        <div class="page-actions">
            <form method="POST" action="{{ route('reports.export-pdf', 'inventory') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> تنزيل PDF
                </button>
            </form>
            <form method="POST" action="{{ route('reports.export-excel', 'inventory') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> تنزيل Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-cube"
                label="إجمالي المنتجات"
                value="{{ $inventoryReport['summary']['total_products'] ?? 0 }}"
                change="منتج" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-weight"
                label="إجمالي الكمية"
                value="{{ number_format($inventoryReport['summary']['total_quantity_kg'] ?? 0, 2) }}"
                change="كج" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-dollar-sign"
                label="قيمة المخزون"
                value="{{ number_format($inventoryReport['summary']['total_value'] ?? 0, 2) }}"
                change="ر.س" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-exclamation-triangle"
                label="منتجات حد أدنى"
                value="{{ $inventoryReport['summary']['low_stock_count'] ?? 0 }}"
                change="منتج" />
        </div>
    </div>

    <!-- Inventory Details -->
    <x-card>
        @slot('header')
            <i class="fas fa-boxes"></i> تفاصيل المخزون
        @endslot

        @if($inventoryReport['products']->count() > 0)
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-hover table-sm">
                    <thead style="position: sticky; top: 0;">
                        <tr style="background: var(--coffee-dark); color: white;">
                            <th>المنتج</th>
                            <th>الفئة</th>
                            <th class="text-end">الكمية (كج)</th>
                            <th class="text-end">سعر الشراء</th>
                            <th class="text-end">قيمة الشراء</th>
                            <th class="text-end">سعر البيع</th>
                            <th class="text-end">نسبة الربح</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryReport['products'] as $product)
                            <tr>
                                <td><strong>{{ $product['name_ar'] ?? $product['name'] }}</strong></td>
                                <td>{{ $product['category'] ?? '-' }}</td>
                                <td class="text-end">{{ number_format($product['current_stock_kg'], 2) }}</td>
                                <td class="text-end">{{ number_format($product['purchase_price_per_kg'], 2) }} ر.س</td>
                                <td class="text-end">{{ number_format($product['stock_value'], 2) }} ر.س</td>
                                <td class="text-end">{{ number_format($product['selling_price_per_kg'], 2) }} ر.س</td>
                                <td class="text-end">
                                    <span class="badge badge-success">{{ number_format($product['profit_margin'], 1) }}%</span>
                                </td>
                                <td>
                                    @if($product['status'] === 'ok')
                                        <span class="badge badge-success">متوفر</span>
                                    @elseif($product['status'] === 'low')
                                        <span class="badge badge-warning">حد أدنى</span>
                                    @else
                                        <span class="badge badge-danger">نفد</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center" style="padding: 30px;">لا توجد منتجات</p>
        @endif
    </x-card>

    <!-- Low Stock Products -->
    @if($inventoryReport['summary']['low_stock_count'] > 0)
        <x-card class="mt-4">
            @slot('header')
                <i class="fas fa-exclamation-triangle"></i> المنتجات تحت الحد الأدنى
            @endslot

            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr style="background: var(--cream-light);">
                            <th>المنتج</th>
                            <th class="text-end">الكمية الحالية</th>
                            <th class="text-end">الحد الأدنى</th>
                            <th class="text-end">الكمية الناقصة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryReport['products'] as $product)
                            @if($product['status'] === 'low' || $product['status'] === 'out')
                                <tr>
                                    <td><strong>{{ $product['name_ar'] }}</strong></td>
                                    <td class="text-end">{{ $product['current_stock_kg'] }} كج</td>
                                    <td class="text-end">{{ $product['minimum_stock_alert'] }} كج</td>
                                    <td class="text-end" style="color: #dc3545;">{{ $product['shortage'] }} كج</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
@endsection
