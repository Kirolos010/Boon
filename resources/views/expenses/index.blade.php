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
                change="ر.س" />
        </div>
        <div class="col-md-3">
            <x-stat-card
                icon="fas fa-calendar-alt"
                label="نفقات الشهر"
                value="{{ number_format(\App\Models\Expense::whereMonth('expense_date', now()->month)->sum('amount'), 2) }}"
                change="ر.س" />
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
                change="ر.س" />
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
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>الوصف</th>
                        <th>الفئة</th>
                        <th>التاريخ</th>
                        <th>المبلغ</th>
                        <th>المسؤول</th>
                        <th>الملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td><strong>{{ $expense->description }}</strong></td>
                            <td>{{ $expense->category->name ?? 'N/A' }}</td>
                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td class="text-end">{{ number_format($expense->amount, 2) }} ر.س</td>
                            <td>{{ $expense->creator->name }}</td>
                            <td><small>{{ Str::limit($expense->notes, 30) }}</small></td>
                            <td>
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: inline;" onsubmit="return confirm('حذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                                <p>لا توجد نفقات</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            {{ $expenses->links() }}
        @endif
    </x-card>
@endsection
