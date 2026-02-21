@extends('layouts.app')

@section('title', 'الموردين')
@section('navbar-title', 'إدارة الموردين')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الموردين</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">الموردين</h1>
            <p class="page-title-subtitle">إدارة الموردين والعلاقات التجارية</p>
        </div>
        <a href="{{ route('settings.suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> مورد جديد
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" icon="check-circle" />
    @endif
    @if(session('error'))
        <x-alert message="{{ session('error') }}" type="danger" icon="exclamation-circle" />
    @endif

    <!-- Search Card -->
    <x-card title="بحث">
        <form method="GET" action="{{ route('settings.suppliers.index') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="البحث عن... (الاسم، البريد، الهاتف)" value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary">
                <i class="fas fa-search"></i> بحث
            </button>
            <a href="{{ route('settings.suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i> مسح
            </a>
        </form>
    </x-card>

    <!-- Suppliers Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>المدينة</th>
                        <th>البلد</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <strong>{{ $supplier->name_ar ?? $supplier->name_en }}</strong>
                                @if($supplier->name_en && $supplier->name_ar)
                                    <br><small class="text-muted">{{ $supplier->name_en }}</small>
                                @endif
                            </td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->city ?? '-' }}</td>
                            <td>{{ $supplier->country ?? '-' }}</td>
                            <td>
                                <a href="{{ route('settings.suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('settings.suppliers.destroy', $supplier) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
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
                            <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا يوجد موردين</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
            {{ $suppliers->links() }}
        @endif
    </x-card>
@endsection
