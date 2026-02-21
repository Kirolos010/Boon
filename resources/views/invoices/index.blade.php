@extends('layouts.app')

@section('title', 'الفواتير')
@section('navbar-title', 'إدارة الفواتير')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الفواتير</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="fas fa-file-invoice text-primary"></i> الفواتير</h1>
            <p class="page-title-subtitle">إدارة فواتير البيع والدفعات</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus-circle"></i> فاتورة جديدة
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" />
    @endif

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('invoices.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث برقم الفاتورة أو العميل..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئية</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
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

    <!-- Invoices Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>التاريخ</th>
                        <th>إجمالي</th>
                        <th>مدفوع</th>
                        <th>متبقي</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td><strong class="text-primary">{{ $invoice->invoice_number }}</strong></td>
                            <td>{{ $invoice->client->name_ar ?? 'بيع سريع' }}</td>
                            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                            <td><strong>{{ number_format($invoice->total, 2) }} ر.س</strong></td>
                            <td>{{ number_format($invoice->amount_paid, 2) }} ر.س</td>
                            <td>{{ number_format($invoice->remaining_balance, 2) }} ر.س</td>
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
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-primary" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($invoice->status !== 'paid')
                                        <button class="btn btn-sm btn-success" onclick="recordPayment({{ $invoice->id }})" title="تسجيل دفعة">
                                            <i class="fas fa-dollar-sign"></i>
                                        </button>
                                    @endif
                                </div>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
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
                                <i class="fas fa-file-invoice" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا توجد فواتير</p>
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
@endsection

@section('scripts')
    <script>
        function recordPayment(invoiceId) {
            alert('سيتم فتح نافذة تسجيل الدفع');
        }
    </script>
@endsection
