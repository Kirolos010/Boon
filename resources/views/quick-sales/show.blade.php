@extends('layouts.app')

@section('title', 'عرض بيع سريع')
@section('navbar-title', 'بيع سريع رقم ' . $sale->invoice_number)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="no-print">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quick-sales.index') }}">البيع السريع</a></li>
            <li class="breadcrumb-item active">عرض البيع</li>
        </ol>
    </nav>

    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" />
    @endif

    <!-- Print View -->
    <div class="print-only">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h3 class="mb-1">فاتورة بيع سريع</h3>
                <div>رقم الفاتورة: {{ $sale->invoice_number }}</div>
                @if($sale->customer_name)
                    <div>العميل: {{ $sale->customer_name }}</div>
                @endif
                <div>التاريخ: {{ $sale->created_at->format('Y-m-d') }}</div>
                <div>الوقت: {{ $sale->created_at->format('H:i') }}</div>
            </div>
            <div class="text-start">
                <div style="font-size: 18px; font-weight: 700; color: #6F4E37;"> الغــــــالــــــى </div>
                <div style="font-size: 14px; color: #8B7355;"> للبن والأعشاب الفاخرة </div>
                <div style="font-size: 12px; color: #999;">El Ghaly Premium Coffee & Herbs</div>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>المنتج</th>
                    <th style="width: 120px;">الكمية</th>
                    <th style="width: 120px;">السعر</th>
                    <th style="width: 120px;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name_ar }}</td>
                    <td>{{ $item->quantity_kg }} كج</td>
                    <td>{{ number_format($item->unit_price, 2) }} ج.م</td>
                    <td>{{ number_format($item->quantity_kg * $item->unit_price, 2) }} ج.م</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-3">
            <div style="min-width: 260px;">
                <div class="d-flex justify-content-between"><span>المجموع الفرعي:</span><strong>{{ number_format($sale->subtotal, 2) }} ج.م</strong></div>
                {{-- Tax disabled
                <div class="d-flex justify-content-between"><span>الضريبة (15%):</span><strong>{{ number_format($sale->tax, 2) }} ج.م</strong></div>
                --}}
                <div class="d-flex justify-content-between border-top mt-2 pt-2"><span><strong>الإجمالي:</strong></span><strong>{{ number_format($sale->total, 2) }} ج.م</strong></div>
            </div>
        </div>

        @if($sale->notes)
            <div class="mt-3"><strong>ملاحظات:</strong> {{ $sale->notes }}</div>
        @endif

        <div class="mt-4 text-center">
            <p class="mb-0"><strong>طريقة الدفع:</strong> {{ $sale->payment_method === 'cash' ? 'نقدي' : ($sale->payment_method === 'card' ? 'بطاقة' : 'تحويل بنكي') }}</p>
        </div>
    </div>

    <!-- Screen View -->
    <div class="print-hide">
        <!-- Sale Details Card -->
        <x-card>
            <div class="invoice-header d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold mb-2"><i class="fas fa-bolt text-success"></i> بيع سريع</h2>
                    <p class="text-muted mb-1">رقم الفاتورة: <span class="fw-bold text-dark">{{ $sale->invoice_number }}</span></p>
                    @if($sale->customer_name)
                        <p class="text-muted mb-1">العميل: <span class="fw-bold text-dark">{{ $sale->customer_name }}</span></p>
                    @endif
                    <p class="text-muted mb-1">التاريخ: <span class="fw-bold text-dark">{{ $sale->created_at->format('Y-m-d') }}</span></p>
                    <p class="text-muted mb-0">الوقت: <span class="fw-bold text-dark">{{ $sale->created_at->format('H:i A') }}</span></p>
                </div>
                <div class="text-start">
                    <h5 class="mb-1">الغالى للبن والأعشاب</h5>
                    <p class="text-muted small mb-0">El Ghaly Coffee & Herbs</p>
                </div>
            </div>

            <!-- Payment Method Badge -->
            <div class="mb-4">
                <h6 class="fw-bold mb-2"><i class="fas fa-credit-card text-info"></i> طريقة الدفع</h6>
                @if($sale->payment_method === 'cash')
                    <span class="badge bg-success fs-6"><i class="fas fa-money-bill"></i> نقدي</span>
                @elseif($sale->payment_method === 'card')
                    <span class="badge bg-primary fs-6"><i class="fas fa-credit-card"></i> بطاقة</span>
                @elseif($sale->payment_method === 'bank_transfer')
                    <span class="badge bg-info fs-6"><i class="fas fa-exchange-alt"></i> تحويل بنكي</span>
                @endif
            </div>

            <!-- Sale Items -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-shopping-cart text-primary"></i> بنود البيع</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>المنتج</th>
                                <th style="width: 120px;">الكمية</th>
                                <th style="width: 120px;">السعر</th>
                                <th style="width: 120px;">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $item->product->name_ar }}</td>
                                <td class="text-center">{{ $item->quantity_kg }} كج</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }} ج.م</td>
                                <td class="text-end fw-bold">{{ number_format($item->quantity_kg * $item->unit_price, 2) }} ج.م</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="d-flex justify-content-end">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>المجموع الفرعي:</span>
                                <span class="fw-bold">{{ number_format($sale->subtotal, 2) }} ج.م</span>
                            </div>
                            {{-- Tax disabled
                            <div class="d-flex justify-content-between mb-2">
                                <span>الضريبة (15%):</span>
                                <span class="fw-bold">{{ number_format($sale->tax, 2) }} ج.م</span>
                            </div>
                            --}}
                            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                <span class="fs-5 fw-bold">الإجمالي:</span>
                                <span class="fs-5 fw-bold text-success">{{ number_format($sale->total, 2) }} ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($sale->notes)
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold mb-2"><i class="fas fa-sticky-note text-warning"></i> ملاحظات</h6>
                <p class="text-muted">{{ $sale->notes }}</p>
            </div>
            @endif
        </x-card>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('quick-sales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-info">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-hide {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: white;
            }
            .sidebar {
                display: none !important;
            }
            .navbar {
                display: none !important;
            }
            .main-content {
                margin-right: 0 !important;
            }
        }
        .print-only {
            display: none;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Auto print when print parameter is present
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1') {
                setTimeout(function() {
                    window.print();
                }, 1000);
            }
        });
    </script>
@endsection
