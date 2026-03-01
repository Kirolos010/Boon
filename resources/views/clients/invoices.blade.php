@extends('layouts.app')

@section('title', 'فواتير العميل')
@section('navbar-title', 'فواتير العميل: ' . $client->name_ar)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">العملاء</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clients.show', $client) }}">{{ $client->name_ar }}</a></li>
            <li class="breadcrumb-item active">الفواتير</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="fas fa-file-invoice text-primary"></i> فواتير العميل</h1>
            <p class="page-title-subtitle">{{ $client->name_ar }} - {{ $invoices->total() }} فاتورة</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus-circle"></i> فاتورة جديدة
        </a>
    </div>

    <!-- Client Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-primary">{{ $invoices->total() }}</div>
                <div class="stat-label">عدد الفواتير</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-success">{{ number_format($client->invoices->sum('total'), 2) }} ج.م</div>
                <div class="stat-label">إجمالي الفواتير</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-info">{{ number_format($client->invoices->sum('amount_paid'), 2) }} ج.م</div>
                <div class="stat-label">إجمالي المدفوع</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-danger">{{ number_format($client->invoices->sum('remaining_balance'), 2) }} ج.م</div>
                <div class="stat-label">إجمالي المتبقي</div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>التاريخ</th>
                        <th>الإجمالي</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td><strong class="text-primary">{{ $invoice->invoice_number }}</strong></td>
                            <td>{{ $invoice->invoice_date->format('Y-m-d H:i') }}</td>
                            <td><strong>{{ number_format($invoice->total, 2) }} ج.م</strong></td>
                            <td>{{ number_format($invoice->amount_paid, 2) }} ج.م</td>
                            <td>
                                @if($invoice->remaining_balance > 0)
                                    <strong class="text-danger">{{ number_format($invoice->remaining_balance, 2) }} ج.م</strong>
                                @else
                                    <span class="text-success">-</span>
                                @endif
                            </td>
                            <td>
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> مدفوعة</span>
                                @elseif($invoice->status === 'partial')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> جزئية</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i> غير مدفوعة</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.show', $invoice) }}?print=1" target="_blank" class="btn btn-sm btn-secondary" title="طباعة">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-primary" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-file-invoice" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا توجد فواتير لهذا العميل</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            {{ $invoices->links() }}
        @endif
    </x-card>

    <!-- Back Button -->
    <div class="mt-3">
        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة إلى بيانات العميل
        </a>
    </div>
@endsection

<style>
    .stat-card {
        padding: 20px;
        border-radius: 8px;
        background: linear-gradient(135deg, #F5E6D3, #E8D7C3);
        border-left: 4px solid #6F4E37;
        text-align: center;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: #8B7355;
        text-transform: uppercase;
    }
</style>
