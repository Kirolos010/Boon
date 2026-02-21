@extends('layouts.app')

@section('title', 'تعديل فاتورة')
@section('navbar-title', 'تعديل فاتورة رقم ' . $invoice->invoice_number)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">الفواتير</a></li>
            <li class="breadcrumb-item active">تعديل الفاتورة</li>
        </ol>
    </nav>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> خطأ في النموذج:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PATCH')

        <div class="row">
            <div class="col-lg-8">
                <x-card>
                    @slot('header')
                        <i class="fas fa-edit"></i> تعديل بيانات الفاتورة
                    @endslot

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_id" class="form-label"><strong>العميل *</strong></label>
                                <select name="client_id" id="client_id" class="form-select form-select-lg" required>
                                    <option value="">اختر العميل</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name_ar }} - الرصيد: {{ number_format($client->balance, 2) }} ر.س
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_date" class="form-label"><strong>تاريخ الفاتورة *</strong></label>
                                <input type="date" name="invoice_date" id="invoice_date"
                                    value="{{ old('invoice_date', $invoice->invoice_date) }}"
                                    class="form-control form-control-lg" required>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary"></i> منتجات الفاتورة</h5>
                        <button type="button" class="btn btn-sm btn-success" onclick="addInvoiceRow()">
                            <i class="fas fa-plus-circle"></i> إضافة منتج
                        </button>
                    </div>

                    <table class="table table-bordered table-hover" id="invoiceItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>المنتج</th>
                                <th style="width: 120px;">الكمية</th>
                                <th style="width: 120px;">السعر</th>
                                <th style="width: 120px;">الإجمالي</th>
                                <th style="width: 50px;"><i class="fas fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems">
                            @foreach($invoice->items as $index => $item)
                            <tr class="invoice-row">
                                <td>
                                    <select name="items[{{ $index }}][product_id]" class="form-control product-select" required onchange="updatePrice(this)">
                                        <option value="">اختر المنتج</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ $product->selling_price_per_kg ?? $product->selling_price }}"
                                                data-stock="{{ $product->current_stock_kg ?? $product->currentStock() }}"
                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name_ar }} (متوفر: {{ $product->current_stock_kg ?? $product->currentStock() }} كج)
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity_kg]"
                                        value="{{ $item->quantity_kg }}"
                                        min="0.01" step="0.01" class="form-control quantity-input" required onchange="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][unit_price]"
                                        value="{{ $item->unit_price }}"
                                        min="0" step="0.01" class="form-control price-input" required onchange="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" value="{{ $item->quantity_kg * $item->unit_price }}"
                                        class="form-control total-input bg-light" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-card>
            </div>

            <div class="col-lg-4">
                <x-card>
                    @slot('header')
                        <i class="fas fa-calculator"></i> ملخص الفاتورة
                    @endslot

                    <div class="summary-row">
                        <span>الإجمالي الفرعي:</span>
                        <strong id="subtotalDisplay">{{ number_format($invoice->subtotal, 2) }} ر.س</strong>
                    </div>
                    <div class="summary-row">
                        <span>الخصم:</span>
                        <strong id="discountDisplay" class="text-success">{{ number_format($invoice->discount ?? 0, 2) }} ر.س</strong>
                    </div>
                    <div class="summary-row">
                        <span>الضريبة (15%):</span>
                        <strong id="taxDisplay">{{ number_format($invoice->tax, 2) }} ر.س</strong>
                    </div>
                    <div class="summary-row border-top pt-2 mt-2">
                        <span class="fs-5">الإجمالي النهائي:</span>
                        <strong id="totalDisplay" class="fs-5 text-primary">{{ number_format($invoice->total, 2) }} ر.س</strong>
                    </div>

                    <input type="hidden" name="total_amount" id="totalAmountInput" value="{{ $invoice->total }}">

                    <hr>

                    <div class="form-group mb-3">
                        <label for="discount" class="form-label"><strong>الخصم</strong></label>
                        <input type="number" name="discount" id="discount"
                            value="{{ old('discount', $invoice->discount ?? 0) }}"
                            step="0.01" min="0" class="form-control" placeholder="0.00">
                    </div>

                    <div class="form-group mb-3">
                        <label for="notes" class="form-label"><strong>ملاحظات</strong></label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"
                            placeholder="ملاحظات على الفاتورة">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                            <i class="fas fa-times-circle"></i> إلغاء
                        </a>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <style>
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        #invoiceItemsTable input,
        #invoiceItemsTable select {
            font-size: 14px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        let rowIndex = {{ count($invoice->items) }};
        const products = @json($products ?? []);

        function addInvoiceRow() {
            const tbody = document.getElementById('invoiceItems');
            const newRow = `
                <tr class="invoice-row">
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-control product-select" required onchange="updatePrice(this)">
                            <option value="">اختر المنتج</option>
                            ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price_per_kg || p.selling_price}" data-stock="${p.current_stock_kg || 0}">${p.name_ar} (${p.current_stock_kg || 0} كج)</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][quantity_kg]" step="0.01" min="0.01" class="form-control quantity-input" required onchange="calculateRow(this)">
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][unit_price]" step="0.01" min="0" class="form-control price-input" required onchange="calculateRow(this)">
                    </td>
                    <td>
                        <input type="number" class="form-control total-input bg-light" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', newRow);
            rowIndex++;
        }

        function updatePrice(select) {
            const row = select.closest('tr');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const priceInput = row.querySelector('.price-input');
            priceInput.value = price || 0;
            calculateRow(row.querySelector('.quantity-input'));
        }

        function calculateRow(input) {
            const row = input.closest('tr');
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = quantity * price;
            row.querySelector('.total-input').value = total.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            const totals = document.querySelectorAll('.total-input');
            let subtotal = 0;
            totals.forEach(input => {
                subtotal += parseFloat(input.value) || 0;
            });

            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const afterDiscount = subtotal - discount;
            const tax = afterDiscount * 0.15;
            const total = afterDiscount + tax;

            document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ر.س';
            document.getElementById('discountDisplay').textContent = discount.toFixed(2) + ' ر.س';
            document.getElementById('taxDisplay').textContent = tax.toFixed(2) + ' ر.س';
            document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ر.س';
            document.getElementById('totalAmountInput').value = total.toFixed(2);
        }

        function removeRow(button) {
            const rows = document.querySelectorAll('.invoice-row');
            if (rows.length > 1) {
                button.closest('tr').remove();
                calculateTotal();
            } else {
                alert('يجب أن تحتوي الفاتورة على منتج واحد على الأقل');
            }
        }

        // Calculate initial totals on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();

            // Add event listener for discount input
            document.getElementById('discount').addEventListener('input', calculateTotal);
        });
    </script>
@endsection
