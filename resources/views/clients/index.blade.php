@extends('layouts.app')

@section('title', 'العملاء')
@section('navbar-title', 'إدارة العملاء')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">العملاء</h1>
            <p class="page-title-subtitle">إدارة بيانات العملاء وسجل مشترياتهم</p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> عميل جديد
        </a>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو الهاتف..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clients Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>البريد</th>
                        <th>إجمالي المشتريات</th>
                        <th>الدين</th>
                        <th>حد الائتمان</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td><strong>{{ $client->name_ar }}</strong></td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->email ?? '-' }}</td>
                            <td>{{ number_format($client->invoices()->sum('total') ?? 0, 2) }} ج.م</td>
                            <td>{{ number_format($client->total_debt, 2) }} ج.م</td>
                            <td>{{ number_format($client->credit_limit, 2) }} ج.م</td>
                            <td>
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
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
                                <i class="fas fa-users" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا توجد عملاء</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            {{ $clients->links() }}
        @endif
    </x-card>
@endsection
