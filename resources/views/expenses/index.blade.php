@extends('layouts.app')

@section('title', 'النفقات')
@section('navbar-title', 'إدارة النفقات')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">النفقات</h1>
            <p class="page-title-subtitle">تسجيل وإدارة نفقات التشغيل</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> نفقة جديدة
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-calendar-day"
                label="نفقات اليوم"
                value="{{ number_format(\App\Models\Expense::whereDate('expense_date', today())->sum('amount'), 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-calendar-alt"
                label="نفقات الشهر"
                value="{{ number_format(\App\Models\Expense::whereMonth('expense_date', now()->month)->sum('amount'), 2) }}"
                change="ج.م" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-list"
                label="عدد النفقات"
                value="{{ \App\Models\Expense::count() }}"
                change="عملية" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-chart-pie"
                label="متوسط النفقة"
                value="{{ number_format(\App\Models\Expense::avg('amount') ?? 0, 2) }}"
                change="ج.م" />
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث عن نفقة..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 30%;">الوصف</th>
                        <th style="width: 15%;">الفئة</th>
                        <th style="width: 12%;">التاريخ</th>
                        <th class="text-end" style="width: 15%;">المبلغ</th>
                        <th style="width: 12%;">المسؤول</th>
                        <th style="width: 8%;">المرجع</th>
                        <th class="text-center" style="width: 8%;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>
                                <strong>{{ $expense->description }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $expense->category->name_ar ?? 'غير محدد' }}</span>
                            </td>
                            <td>{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : '-' }}</td>
                            <td class="text-end">
                                <strong class="text-danger">{{ number_format($expense->amount, 2) }} ج.م</strong>
                            </td>
                            <td>{{ $expense->creator->name ?? '-' }}</td>
                            <td>
                                @if($expense->reference)
                                    <small class="badge bg-info">{{ $expense->reference }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-primary" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="deleteExpense({{ $expense->id }})" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 50px;">
                                <i class="fas fa-receipt" style="font-size: 48px; opacity: 0.2;"></i>
                                <p class="mt-3 mb-0">لا توجد نفقات مسجلة</p>
                                <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i> إضافة أول نفقة
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="mt-3">
                {{ $expenses->links() }}
            </div>
        @endif
    </x-card>

    <script>
        function deleteExpense(id) {
            if (confirm('هل أنت متأكد من حذف هذه النفقة؟')) {
                fetch(`/expenses/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert('حدث خطأ أثناء الحذف');
                    }
                })
                .catch(error => {
                    alert('حدث خطأ أثناء الحذف');
                });
            }
        }
    </script>
@endsection
