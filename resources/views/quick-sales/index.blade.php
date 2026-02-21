@extends('layouts.app')

@section('title', 'البيع السريع')
@section('navbar-title', 'البيع السريع')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">البيع السريع</h1>
            <p class="page-title-subtitle">تسجيل المبيعات الفورية والنقدية</p>
        </div>
        <a href="{{ route('quick-sales.create') }}" class="btn btn-success btn-lg">
            <i class="fas fa-bolt"></i> بيع سريع جديد
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-bolt"
                label="اليوم"
                value="{{ \App\Models\Invoice::where('type', 'quick')->whereDate('created_at', today())->count() }}"
                change="عمليات بيع سريعة" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-dollar-sign"
                label="إجمالي اليوم"
                value="{{ number_format(\App\Models\Invoice::where('type', 'quick')->whereDate('created_at', today())->sum('total'), 2) }}"
                change="ر.س" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-calendar"
                label="هذا الشهر"
                value="{{ \App\Models\Invoice::where('type', 'quick')->whereMonth('created_at', now()->month)->count() }}"
                change="عملية بيع" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-chart-line"
                label="المجموع الشهري"
                value="{{ number_format(\App\Models\Invoice::where('type', 'quick')->whereMonth('created_at', now()->month)->sum('total'), 2) }}"
                change="ر.س" />
        </div>
    </div>

    <!-- Quick Sales Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>التاريخ</th>
                        <th>الوقت</th>
                        <th>عدد العناصر</th>
                        <th>الإجمالي</th>
                        <th>طريقة الدفع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quickSales as $sale)
                        <tr>
                            <td><strong>{{ $sale->invoice_number }}</strong></td>
                            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                            <td>{{ $sale->created_at->format('H:i') }}</td>
                            <td>{{ $sale->items->count() }}</td>
                            <td>{{ number_format($sale->total, 2) }} ر.س</td>
                            <td>
                                <span class="badge badge-info">{{ $sale->payment_method }}</span>
                            </td>
                            <td>
                                <a href="{{ route('quick-sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                                <p>لا توجد عمليات بيع سريع</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quickSales->hasPages())
            {{ $quickSales->links() }}
        @endif
    </x-card>
@endsection
