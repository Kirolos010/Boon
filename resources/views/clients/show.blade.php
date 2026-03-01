@extends('layouts.app')

@section('title', 'عرض العميل')
@section('navbar-title', 'عرض العميل: ' . $client->name_ar)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">العملاء</a></li>
            <li class="breadcrumb-item active">{{ $client->name_ar }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Client Information -->
        <div class="col-lg-4">
            <x-card>
                @slot('header')
                    <i class="fas fa-user"> </i> معلومات العميل
                @endslot

                <div class="text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3" style="
                        width: 100px;
                        height: 100px;
                        background: linear-gradient(135deg, #8B7355, #D2B48C);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 36px;
                        font-weight: bold;
                    ">
                        {{ strtoupper(substr($client->name_ar, 0, 1)) }}
                    </div>
                    <h5 class="mb-1">{{ $client->name_ar }}</h5>
                    <p class="text-muted mb-3">{{ $client->name }}</p>
                </div>

                <div class="mb-3">
                    <small class="text-muted">الهاتف الأساسي</small>
                    <p class="mb-0">
                        <i class="fas fa-phone text-primary"></i>
                        {{ $client->phone }}
                    </p>
                </div>

                @if($client->phone_2)
                    <div class="mb-3">
                        <small class="text-muted">الهاتف الثاني</small>
                        <p class="mb-0">
                            <i class="fas fa-phone text-primary"></i>
                            {{ $client->phone_2 }}
                        </p>
                    </div>
                @endif

                @if($client->address_ar)
                    <div class="mb-3">
                        <small class="text-muted">العنوان</small>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            {{ $client->address_ar }}
                        </p>
                    </div>
                @endif

                <hr>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الحد الائتماني:</span>
                        <strong>{{ number_format($client->credit_limit, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">إجمالي الدين:</span>
                        <strong class="text-danger">{{ number_format($client->total_debt, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">الرصيد المتاح:</span>
                        <strong class="text-success">{{ number_format($client->credit_limit - $client->total_debt, 2) }} ج.م</strong>
                    </div>
                </div>

                <div class="alert alert-info" role="alert">
                    <strong>الحالة:</strong>
                    @if($client->is_active)
                        <span class="badge bg-success">نشط</span>
                    @else
                        <span class="badge bg-danger">معطّل</span>
                    @endif
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('clients.invoices', $client) }}" class="btn btn-success">
                        <i class="fas fa-file-invoice"></i> عرض الفواتير
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> حذف العميل
                        </button>
                    </form>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Invoices -->
        <div class="col-lg-8">
            <x-card>
                @slot('header')
                    <i class="fas fa-file-invoice"></i> فواتير العميل
                @endslot

                @if($client->invoices->count() > 0)
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
                                @foreach($client->invoices as $invoice)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $invoice->invoice_number }}</strong>
                                        </td>
                                        <td>{{ $invoice->invoice_date->format('Y-m-d H:i') }}</td>
                                        <td>{{ number_format($invoice->total, 2) }} ج.م</td>
                                        <td>{{ number_format($invoice->amount_paid, 2) }} ج.م</td>
                                        <td>{{ number_format($invoice->remaining_balance, 2) }} ج.م</td>
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
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-value" style="color: #6F4E37;">
                                    {{ $client->invoices->count() }}
                                </div>
                                <div class="stat-label">عدد الفواتير</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-value" style="color: #28a745;">
                                    {{ number_format($client->invoices->sum('amount_paid'), 2) }} ج.م
                                </div>
                                <div class="stat-label">إجمالي المدفوع</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-value" style="color: #dc3545;">
                                    {{ number_format($client->invoices->sum('remaining_balance'), 2) }} ج.م
                                </div>
                                <div class="stat-label">إجمالي المتبقي</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted" style="padding: 30px;">
                        <i class="fas fa-file-invoice" style="font-size: 40px; opacity: 0.3;"></i>
                        <p style="margin-top: 10px;">لا توجد فواتير لهذا العميل</p>
                    </div>
                @endif
            </x-card>

            @if($client->notes)
                <x-card>
                    @slot('header')
                        <i class="fas fa-sticky-note"></i> ملاحظات
                    @endslot

                    <p>{{ $client->notes }}</p>
                </x-card>
            @endif
        </div>
    </div>
@endsection

<style>
    .stat-card {
        padding: 20px;
        border-radius: 8px;
        background: linear-gradient(135deg, #F5E6D3, #E8D7C3);
        border-left: 4px solid #6F4E37;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: #8B7355;
        text-transform: uppercase;
    }

    .avatar-lg {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>
