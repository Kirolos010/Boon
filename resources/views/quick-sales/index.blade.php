@extends('layouts.app')

@section('title', 'البيع السريع')
@section('navbar-title', 'إدارة البيع السريع')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">البيع السريع</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="fas fa-bolt text-success"></i> البيع السريع</h1>
            <p class="page-title-subtitle">إدارة المبيعات السريعة والنقدية</p>
        </div>
        <a href="{{ route('quick-sales.create') }}" class="btn btn-success btn-lg">
            <i class="fas fa-plus-circle"></i> بيع سريع جديد
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" />
    @endif

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('quick-sales.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث برقم الفاتورة..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="payment_method" class="form-select">
                        <option value="">-- جميع طرق الدفع --</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" placeholder="من تاريخ" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" placeholder="إلى تاريخ" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('quick-sales.index') }}" class="btn btn-outline-secondary flex-fill">
                        <i class="fas fa-rotate-left"></i> إعادة
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Sales Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>التاريخ</th>
                        <th>الوقت</th>
                        <th>عدد المنتجات</th>
                        <th>إجمالي</th>
                        <th>طريقة الدفع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quickSales as $sale)
                        <tr>
                            <td><strong class="text-success">{{ $sale->invoice_number }}</strong></td>
                            <td>{{ $sale->customer_name ?? 'عميل عابر' }}</td>
                            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                            <td>{{ $sale->created_at->format('H:i') }}</td>
                            <td><span class="badge bg-secondary">{{ $sale->items->count() }}</span></td>
                            <td><strong>{{ number_format($sale->total, 2) }} ج.م</strong></td>
                            <td>
                                @if($sale->payment_method === 'cash')
                                    <span class="badge bg-success"><i class="fas fa-money-bill"></i> نقدي</span>
                                @elseif($sale->payment_method === 'card')
                                    <span class="badge bg-primary"><i class="fas fa-credit-card"></i> بطاقة</span>
                                @elseif($sale->payment_method === 'bank_transfer')
                                    <span class="badge bg-info"><i class="fas fa-exchange-alt"></i> تحويل بنكي</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('quick-sales.show', $sale) }}" class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('quick-sales.show', $sale) }}?print=1" target="_blank" class="btn btn-sm btn-secondary" title="طباعة">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                                <form action="{{ route('quick-sales.destroy', $sale) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا البيع؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-bolt" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا توجد عمليات بيع سريع</p>
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
