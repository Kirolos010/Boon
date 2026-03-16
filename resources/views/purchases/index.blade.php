@extends('layouts.app')

@section('title', 'طلبات الشراء')
@section('navbar-title', 'إدارة طلبات الشراء')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">طلبات الشراء</h1>
            <p class="page-title-subtitle">إدارة طلبات الشراء من الموردين</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> طلب شراء جديد
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-hourglass-half"
                label="طلبات منتظرة"
                value="{{ \App\Models\Purchase::where('status', 'pending')->count() }}"
                changeClass="warning" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-check-circle"
                label="طلبات مستلمة"
                value="{{ \App\Models\Purchase::where('status', 'received')->count() }}"
                changeClass="positive" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-dolly"
                label="إجمالي الطلبات"
                value="{{ \App\Models\Purchase::count() }}"
                changeClass="positive" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-dollar-sign"
                label="إجمالي التكاليف"
                value="{{ number_format(\App\Models\Purchase::sum('total_cost'), 2) }}"
                changeClass="positive" />
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('purchases.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="بحث برقم الطلب أو المورد..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>منتظر</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>مستلم</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئي</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-rotate-left"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchases Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المورد</th>
                        <th>التاريخ</th>
                        <th>عدد العناصر</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td><strong>{{ $purchase->purchase_number }}</strong></td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                            <td>{{ $purchase->items->count() }}</td>
                            <td>{{ number_format($purchase->total_cost, 2) }} ج.م</td>
                            <td>
                                @if($purchase->status === 'pending')
                                    <span class="badge badge-warning">منتظر</span>
                                @elseif($purchase->status === 'received')
                                    <span class="badge badge-success">مستلم</span>
                                @else
                                    <span class="badge badge-info">جزئي</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-secondary" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($purchase->status === 'pending')
                                    <button onclick="receivePurchase({{ $purchase->id }})" class="btn btn-sm btn-success" title="استلام">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-dolly" style="font-size: 40px; opacity: 0.3;"></i>
                                <p>لا توجد طلبات شراء</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            {{ $purchases->links() }}
        @endif
    </x-card>
@endsection

@section('scripts')
    <script>
        function receivePurchase(purchaseId) {
            alert('سيتم فتح نافذة استلام الطلب');
        }
    </script>
@endsection
