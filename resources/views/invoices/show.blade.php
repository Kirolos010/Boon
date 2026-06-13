@extends('layouts.app')

@section('title', 'عرض فاتورة')
@section('navbar-title', 'عرض فاتورة رقم ' . $invoice->invoice_number)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="no-print">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">الفواتير</a></li>
            <li class="breadcrumb-item active">عرض الفاتورة</li>
        </ol>
    </nav>

    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" />
    @endif

        <div class="print-only">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h3 class="mb-1">فاتورة مبيعات</h3>
                    <div>رقم الفاتورة: {{ $invoice->invoice_number }}</div>
                    <div>التاريخ: {{ $invoice->invoice_date->format('Y-m-d H:i') }}</div>
                    <div>
                        حالة الفاتورة:
                        @if($invoice->status === 'paid')
                            <span class="badge bg-success">مدفوعة</span>
                        @elseif($invoice->status === 'partial')
                            <span class="badge bg-warning text-dark">مدفوعة جزئياً</span>
                        @else
                            <span class="badge bg-danger">غير مدفوعة</span>
                        @endif
                    </div>
                </div>
                <div class="text-start">
                    <div style="font-size: 18px; font-weight: 700; color: #6F4E37;"> الغــــالـــى </div>
                    <div style="font-size: 14px; color: #8B7355;"> للبن والأعشاب الفاخرة </div>
                    <div style="font-size: 12px; color: #999;">El Ghaly Premium Coffee & Herbs</div>
                </div>
            </div>

            <div class="mb-3">
                <div><strong>العميل:</strong> {{ $invoice->client->name_ar }}</div>
                @if($invoice->client->phone)
                    <div><strong>الهاتف:</strong> {{ $invoice->client->phone }}</div>
                @endif
                @if($invoice->client->email)
                    <div><strong>البريد:</strong> {{ $invoice->client->email }}</div>
                @endif
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
                    @foreach($invoice->items as $index => $item)
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
                    <div class="d-flex justify-content-between"><span>المجموع الفرعي:</span><strong>{{ number_format($invoice->subtotal, 2) }} ج.م</strong></div>
                    @if($invoice->discount > 0)
                        <div class="d-flex justify-content-between"><span>الخصم:</span><strong>- {{ number_format($invoice->discount, 2) }} ج.م</strong></div>
                    @endif
                    {{-- Tax disabled
                    <div class="d-flex justify-content-between"><span>الضريبة (15%):</span><strong>{{ number_format($invoice->tax, 2) }} ج.م</strong></div>
                    --}}
                    <div class="d-flex justify-content-between border-top mt-2 pt-2"><span><strong>الإجمالي:</strong></span><strong>{{ number_format($invoice->total, 2) }} ج.م</strong></div>
                </div>
            </div>

            @if($invoice->notes)
                <div class="mt-3"><strong>ملاحظات:</strong> {{ $invoice->notes }}</div>
            @endif
        </div>

        <div class="print-hide">

    <!-- Invoice Details Card -->
    <x-card>
        <div class="invoice-header d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
            <div>
                <h2 class="fw-bold mb-2"><i class="fas fa-file-invoice text-primary"></i> فاتورة مبيعات</h2>
                <p class="text-muted mb-1">رقم الفاتورة: <span class="fw-bold text-dark">{{ $invoice->invoice_number }}</span></p>
                <p class="text-muted mb-0">التاريخ: <span class="fw-bold text-dark">{{ $invoice->invoice_date->format('Y-m-d H:i') }}</span></p>
                <p class="mt-2 mb-0">
                    <strong>حالة الفاتورة:</strong>
                    @if($invoice->status === 'paid')
                        <span class="badge bg-success">مدفوعة</span>
                    @elseif($invoice->status === 'partial')
                        <span class="badge bg-warning text-dark">مدفوعة جزئياً</span>
                    @else
                        <span class="badge bg-danger">غير مدفوعة</span>
                    @endif
                </p>
            </div>
            <div class="text-start">
                <h5 class="mb-1">الغالى للبن والأعشاب</h5>
                <p class="text-muted small mb-0"> El Ghaly Coffee & Herbs</p>
            </div>
        </div>

        <!-- Client Information -->
        <div class="border-top border-bottom py-3 mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-user text-primary"></i> معلومات العميل</h5>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>الاسم:</strong> {{ $invoice->client->name_ar }}</p>
                    @if($invoice->client->email)
                        <p class="mb-2"><strong>البريد:</strong> {{ $invoice->client->email }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    @if($invoice->client->phone)
                        <p class="mb-2"><strong>الهاتف:</strong> {{ $invoice->client->phone }}</p>
                    @endif
                    <p class="mb-0">
                        <strong>الرصيد الحالي:</strong>
                        <span class="fw-bold {{ $invoice->client->balance > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($invoice->client->balance, 2) }} ج.م
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-shopping-cart text-primary"></i> بنود الفاتورة</h5>
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
                        @foreach($invoice->items as $index => $item)
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
                            <span class="fw-bold">{{ number_format($invoice->subtotal, 2) }} ج.م</span>
                        </div>
                        @if($invoice->discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>الخصم:</span>
                            <span class="fw-bold">- {{ number_format($invoice->discount, 2) }} ج.م</span>
                        </div>
                        @endif
                        {{-- Tax disabled
                        <div class="d-flex justify-content-between mb-2">
                            <span>الضريبة (15%):</span>
                            <span class="fw-bold">{{ number_format($invoice->tax, 2) }} ج.م</span>
                        </div>
                        --}}
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span class="fs-5 fw-bold">الإجمالي:</span>
                            <span class="fs-5 fw-bold text-primary">{{ number_format($invoice->total, 2) }} ج.م</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($invoice->notes)
        <div class="mt-4 pt-3 border-top">
            <h6 class="fw-bold mb-2"><i class="fas fa-sticky-note text-warning"></i> ملاحظات</h6>
            <p class="text-muted">{{ $invoice->notes }}</p>
        </div>
        @endif
    </x-card>

    <!-- Payments Section -->
    <x-card>
        @slot('header')
            <div class="d-flex justify-content-between align-items-center">
                <span><i class="fas fa-money-bill-wave"></i> سجل الدفعات</span>
                @if($invoice->remaining_balance > 0)
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-plus"></i> تسجيل دفعة جديدة
                    </button>
                @endif
            </div>
        @endslot

        @if($invoice->payments && $invoice->payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date }}</td>
                            <td class="fw-bold">{{ number_format($payment->amount, 2) }} ج.م</td>
                            <td>
                                @if($payment->payment_method === 'cash')
                                    <span class="badge bg-success"><i class="fas fa-money-bill"></i> نقدي</span>
                                @elseif($payment->payment_method === 'check')
                                    <span class="badge bg-primary"><i class="fas fa-money-check"></i> شيك</span>
                                @elseif($payment->payment_method === 'transfer')
                                    <span class="badge bg-info"><i class="fas fa-exchange-alt"></i> تحويل بنكي</span>
                                @elseif($payment->payment_method === 'card')
                                    <span class="badge bg-primary"><i class="fas fa-credit-card"></i> بطاقة</span>
                                @elseif($payment->payment_method === 'bank_transfer')
                                    <span class="badge bg-info"><i class="fas fa-exchange-alt"></i> تحويل بنكي</span>
                                @else
                                    <span class="badge bg-secondary">{{ $payment->payment_method }}</span>
                                @endif
                            </td>
                            <td>{{ $payment->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-muted py-4">
                <i class="fas fa-info-circle"></i> لا توجد دفعات مسجلة
            </p>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>حالة الفاتورة:</strong> غير مدفوعة
            </div>
        @endif

        <!-- Payment Summary -->
        <div class="row mt-3 g-3">
            <div class="col-md-4">
                <div class="card bg-primary bg-opacity-10 border-primary">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2 small">إجمالي الفاتورة</p>
                        <h4 class="fw-bold text-primary mb-0">{{ number_format($invoice->total, 2) }} ج.م</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success bg-opacity-10 border-success">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2 small">المدفوع</p>
                        <h4 class="fw-bold text-success mb-0">{{ number_format($invoice->amount_paid, 2) }} ج.م</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-{{ $invoice->remaining_balance > 0 ? 'danger' : 'secondary' }} bg-opacity-10 border-{{ $invoice->remaining_balance > 0 ? 'danger' : 'secondary' }}">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2 small">المتبقي</p>
                        <h4 class="fw-bold text-{{ $invoice->remaining_balance > 0 ? 'danger' : 'secondary' }} mb-0">{{ number_format($invoice->remaining_balance, 2) }} ج.م</h4>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
        <div class="d-flex gap-2">
            <button onclick="printInvoiceDocument()" class="btn btn-info">
                <i class="fas fa-print"></i> طباعة
            </button>
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-money-bill-wave text-success"></i> تسجيل دفعة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoices.record-payment', $invoice) }}" method="POST" id="paymentFormShow">
                    @csrf
                    <div class="modal-body">
                        <!-- Payment Status Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">نوع الدفع *</label>
                            <select id="payment_status_show" class="form-select form-select-lg">
                                <option value="full" selected>دفع المبلغ كامل</option>
                                <option value="partial">دفع جزء من المبلغ</option>
                            </select>
                        </div>

                        <!-- Amount Input (hidden initially for full payment) -->
                        <div class="mb-3" id="amountInputGroupShow" style="display: none;">
                            <label class="form-label fw-bold">المبلغ *</label>
                            <input type="number" name="amount" id="paymentAmountShow" step="0.01" min="0.01"
                                max="{{ $invoice->remaining_balance }}"
                                value="{{ $invoice->remaining_balance }}"
                                class="form-control form-control-lg" required>
                            <small class="text-muted">الحد الأقصى: <span id="maxAmountTextShow">{{ number_format($invoice->remaining_balance, 2) }}</span> ج.م</small>
                        </div>

                        <!-- Hidden input for full payment amount -->
                        <input type="hidden" name="amount_full" id="amountFullShow" value="{{ $invoice->remaining_balance }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">طريقة الدفع *</label>
                            <select name="payment_method" class="form-select form-select-lg" required>
                                <option value="cash">نقدي</option>
                                <option value="check">شيك</option>
                                <option value="transfer">تحويل بنكي</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">تاريخ الدفع *</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                                class="form-control form-control-lg" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات</label>
                            <textarea name="notes" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> حفظ الدفعة
                        </button>
                    </div>
                </form>
            </div>
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

    <script>
        const remainingBalanceShow = {{ $invoice->remaining_balance }};
        const originalDocumentTitle = document.title;
        const printDocumentTitle = @json(($invoice->client->name_ar ?? 'عميل') . ' - ' . $invoice->invoice_number);
        let paymentModalInstanceShow = null;

        function printInvoiceDocument() {
            document.title = printDocumentTitle;
            setTimeout(function () {
                window.print();
            }, 50);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('paymentModal');
            const form = document.getElementById('paymentFormShow');
            const paymentStatusSelect = document.getElementById('payment_status_show');
            const paymentAmountInput = document.getElementById('paymentAmountShow');

            // Initialize modal only ONCE
            paymentModalInstanceShow = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            // Handle payment status change
            if (paymentStatusSelect) {
                paymentStatusSelect.addEventListener('change', function() {
                    handlePaymentStatusChangeShow();
                });
            }

            // Validate amount while typing
            if (paymentAmountInput) {
                paymentAmountInput.addEventListener('input', function() {
                    validatePaymentAmountShow();
                });
            }

            // Handle form submission
            if (form) {
                form.addEventListener('submit', function(e) {
                    const paymentStatus = paymentStatusSelect.value;

                    if (paymentStatus === 'full') {
                        document.getElementById('amountFullShow').value = remainingBalanceShow.toFixed(2);
                        paymentAmountInput.value = remainingBalanceShow.toFixed(2);
                    }
                });
            }

            // Clean up when modal closes
            modalElement.addEventListener('hidden.bs.modal', function() {
                // Reset form
                form.reset();

                // Reset all controls
                paymentStatusSelect.value = 'full';
                paymentAmountInput.value = remainingBalanceShow.toFixed(2);
                paymentAmountInput.removeAttribute('name');
                document.getElementById('amountFullShow').setAttribute('name', 'amount');
                document.getElementById('amountInputGroupShow').style.display = 'none';

                // Ensure no modal backdrop remains
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Restore body scrolling
                document.body.style.overflow = '';
                document.body.classList.remove('modal-open');
            });
        });

        function handlePaymentStatusChangeShow() {
            const paymentStatus = document.getElementById('payment_status_show').value;
            const amountInput = document.getElementById('paymentAmountShow');
            const amountInputGroup = document.getElementById('amountInputGroupShow');
            const amountFullInput = document.getElementById('amountFullShow');

            if (paymentStatus === 'full') {
                // Full payment
                amountInput.value = remainingBalanceShow.toFixed(2);
                amountInput.removeAttribute('name');
                amountFullInput.setAttribute('name', 'amount');
                amountInputGroup.style.display = 'none';
            } else {
                // Partial payment
                amountInput.value = '';
                amountInput.setAttribute('name', 'amount');
                amountFullInput.removeAttribute('name');
                amountInputGroup.style.display = 'block';
                amountInput.focus();
            }
        }

        function validatePaymentAmountShow() {
            const amountInput = document.getElementById('paymentAmountShow');
            const enteredAmount = parseFloat(amountInput.value);

            if (enteredAmount > remainingBalanceShow) {
                amountInput.value = remainingBalanceShow.toFixed(2);
            }
        }

        // Auto print when print parameter is present
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1') {
                setTimeout(function() {
                    printInvoiceDocument();
                }, 1000);
            }
        });

        window.addEventListener('afterprint', function() {
            document.title = originalDocumentTitle;
        });
    </script>
@endsection
