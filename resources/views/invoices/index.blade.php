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
                            <td><strong>{{ number_format($invoice->total, 2) }} ج.م</strong></td>
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
                                    @if($invoice->status !== 'paid')
                                        <button class="btn btn-sm btn-success"
                                            onclick="openPaymentModal({{ $invoice->id }}, {{ $invoice->remaining_balance }}, '{{ $invoice->invoice_number }}')"
                                            title="تسجيل دفعة">
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
                <form id="paymentForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <strong>فاتورة رقم:</strong> <span id="modalInvoiceNumber"></span><br>
                            <strong>المبلغ المتبقي:</strong> <span id="modalRemainingAmount"></span> ج.م
                        </div>

                        <div class="mb-3">
                            <label for="payment_status_modal" class="form-label fw-bold">نوع الدفع *</label>
                            <select id="payment_status_modal" class="form-select form-select-lg">
                                <option value="full">دفع المبلغ كامل</option>
                                <option value="partial">دفع جزء من المبلغ</option>
                            </select>
                        </div>

                        <div class="mb-3" id="amountInputGroup" style="display: none;">
                            <label class="form-label fw-bold">المبلغ *</label>
                            <input type="number" name="amount" id="paymentAmount" step="0.01" min="0.01"
                                class="form-control form-control-lg" required>
                            <small class="text-muted">الحد الأقصى: <span id="maxAmountText"></span> ج.م</small>
                        </div>

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
@endsection

@section('scripts')
    <script>
        let currentInvoiceId = null;
        let currentRemainingBalance = 0;
        let paymentModalInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('paymentModal');
            const form = document.getElementById('paymentForm');
            const paymentStatusSelect = document.getElementById('payment_status_modal');
            const paymentAmountInput = document.getElementById('paymentAmount');

            // Initialize modal only ONCE with default settings
            paymentModalInstance = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            // Handle payment status change
            if (paymentStatusSelect) {
                paymentStatusSelect.addEventListener('change', function() {
                    handlePaymentStatusChange();
                });
            }

            // Validate amount while typing
            if (paymentAmountInput) {
                paymentAmountInput.addEventListener('input', function() {
                    validatePaymentAmount();
                });
            }

            // Handle form submission - ensure amount is set correctly
            if (form) {
                form.addEventListener('submit', function(e) {
                    const paymentStatus = paymentStatusSelect.value;

                    // If full payment, set correct amount
                    if (paymentStatus === 'full') {
                        paymentAmountInput.value = currentRemainingBalance.toFixed(2);
                    }
                });
            }

            // Clean up completely when modal is about to hide
            modalElement.addEventListener('hide.bs.modal', function() {
                // Remove backdrop immediately
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            });

            // Reset state after modal completely closes
            modalElement.addEventListener('hidden.bs.modal', function() {
                // Clear form
                form.reset();

                // Reset all controls
                paymentStatusSelect.value = 'full';
                paymentAmountInput.value = currentRemainingBalance.toFixed(2);
                paymentAmountInput.readOnly = true;
                document.getElementById('amountInputGroup').style.display = 'none';

                // Ensure no modal backdrop remains
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Ensure body scrolling is restored
                document.body.style.overflow = '';
                document.body.classList.remove('modal-open');
            });
        });

        function openPaymentModal(invoiceId, remainingBalance, invoiceNumber) {
            // Guard: Check if instance is ready
            if (!paymentModalInstance) {
                console.error('Modal instance not initialized');
                return;
            }

            currentInvoiceId = invoiceId;
            currentRemainingBalance = parseFloat(remainingBalance);

            // Get elements
            const form = document.getElementById('paymentForm');
            const modalElement = document.getElementById('paymentModal');

            // Step 1: Hide any existing modal and backdrop
            const existingBackdrop = document.querySelector('.modal-backdrop');
            if (existingBackdrop) {
                existingBackdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';

            // Step 2: Reset form completely
            form.reset();

            // Step 3: Update modal content
            document.getElementById('modalInvoiceNumber').textContent = invoiceNumber;
            document.getElementById('modalRemainingAmount').textContent = currentRemainingBalance.toFixed(2);
            document.getElementById('maxAmountText').textContent = currentRemainingBalance.toFixed(2);

            // Step 4: Set form action
            form.action = `/invoices/${invoiceId}/record-payment`;

            // Step 5: Reset payment form to default state
            document.getElementById('payment_status_modal').value = 'full';
            document.getElementById('paymentAmount').value = currentRemainingBalance.toFixed(2);
            document.getElementById('paymentAmount').readOnly = true;
            document.getElementById('amountInputGroup').style.display = 'none';

            // Step 6: Set default payment date
            const dateInput = form.querySelector('[name="payment_date"]');
            if (dateInput) {
                dateInput.value = new Date().toISOString().split('T')[0];
            }

            // Step 7: Show modal with small delay to ensure DOM is ready
            setTimeout(() => {
                paymentModalInstance.show();
            }, 50);
        }

        function handlePaymentStatusChange() {
            const paymentStatus = document.getElementById('payment_status_modal').value;
            const amountInput = document.getElementById('paymentAmount');
            const amountGroup = document.getElementById('amountInputGroup');

            if (paymentStatus === 'full') {
                amountInput.value = currentRemainingBalance.toFixed(2);
                amountInput.readOnly = true;
                amountGroup.style.display = 'none';
            } else {
                amountInput.value = '';
                amountInput.readOnly = false;
                amountGroup.style.display = 'block';
                amountInput.focus();
                amountInput.max = currentRemainingBalance.toFixed(2);
            }
        }

        function validatePaymentAmount() {
            const paymentAmount = document.getElementById('paymentAmount');
            const value = parseFloat(paymentAmount.value);

            if (value > currentRemainingBalance) {
                paymentAmount.value = currentRemainingBalance.toFixed(2);
            }
        }
    </script>
@endsection
