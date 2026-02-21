@extends('layouts.app')

@section('title', 'إنشاء فاتورة مبيعات')
@section('navbar-title', 'إنشاء فاتورة مبيعات')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">الفواتير</a></li>
            <li class="breadcrumb-item active">فاتورة جديدة</li>
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

    <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <x-card>
                    @slot('header')
                        <i class="fas fa-file-invoice"></i> بيانات الفاتورة
                    @endslot

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="client_id" class="form-label"><strong>العميل</strong></label>
                                <select name="client_id" id="clientSelect" class="form-select form-select-lg" required>
                                    <option value="">اختر العميل</option>
                                    @if($clients && count($clients) > 0)
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name_ar }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('client_id')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="invoice_date" class="form-label"><strong>تاريخ الفاتورة</strong></label>
                                <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" class="form-control form-control-lg" required>
                                @error('invoice_date')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
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
                                <th style="width: 120px;">الكمية (كج)</th>
                                <th style="width: 120px;">السعر</th>
                                <th style="width: 120px;">الإجمالي</th>
                                <th style="width: 50px;"><i class="fas fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems">
                            <tr class="invoice-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-control product-select" required onchange="updatePrice(this, 0)">
                                        <option value="">اختر المنتج</option>
                                        @if($products && count($products) > 0)
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price_per_kg }}">
                                                    {{ $product->name_ar }} ({{ $product->current_stock_kg }} كج)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity_kg]" step="0.01" min="0.01" class="form-control quantity-input" required onchange="calculateRow(0)">
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_price]" step="0.01" class="form-control price-input" required onchange="calculateRow(0)">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control total-input" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
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
                        <strong id="subtotalDisplay">0.00 ر.س</strong>
                    </div>
                    <div class="summary-row">
                        <span>الضريبة (15%):</span>
                        <strong id="taxDisplay">0.00 ر.س</strong>
                    </div>
                    <div class="summary-row" style="border-top: 2px solid var(--coffee-dark); padding-top: 10px; margin-top: 10px;">
                        <span style="font-size: 18px;">الإجمالي النهائي:</span>
                        <strong id="totalDisplay" style="font-size: 20px; color: var(--coffee-dark);">0.00 ر.س</strong>
                    </div>

                    <input type="hidden" name="total_amount" id="totalAmountInput">

                    <hr>

                    <div class="form-group mb-3">
                        <label for="amount_paid" class="form-label"><strong>المبلغ المدفوع</strong></label>
                        <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', 0) }}" step="0.01" min="0" class="form-control" placeholder="0.00">
                        @error('amount_paid')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="payment_method" class="form-label"><strong>طريقة الدفع</strong></label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="cash" selected>نقداً</option>
                            <option value="check">شيك</option>
                            <option value="transfer">تحويل بنكي</option>
                            <option value="other">أخرى</option>
                        </select>
                        @error('payment_method')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="discount" class="form-label"><strong>الخصم</strong></label>
                        <input type="number" name="discount" id="discount" value="{{ old('discount', 0) }}" step="0.01" min="0" class="form-control" placeholder="0.00">
                        @error('discount')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <hr>

                    <div class="form-group mb-3">
                        <label for="notes" class="form-label"><strong>ملاحظات</strong></label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="ملاحظات على الفاتورة">{{ old('notes') }}</textarea>

                    <div class="d-grid gap-2" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check-circle"></i> إنشاء الفاتورة
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
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

    <script>
        let rowIndex = 1;
        const products = @json($products ?? []);

        // Handle form submission
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.invoice-row');
            let isValid = true;
            let errorMsg = '';

            rows.forEach((row, index) => {
                const productSelect = row.querySelector('[name*="product_id"]');
                const quantityInput = row.querySelector('[name*="quantity_kg"]');
                const priceInput = row.querySelector('[name*="price_per_kg"]');

                if (!productSelect.value) {
                    isValid = false;
                    errorMsg += `\n- سطر ${index + 1}: اختر منتج`;
                }
                if (!quantityInput.value || parseFloat(quantityInput.value) <= 0) {
                    isValid = false;
                    errorMsg += `\n- سطر ${index + 1}: أدخل كمية صحيحة`;
                }
                if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
                    isValid = false;
                    errorMsg += `\n- سطر ${index + 1}: السعر غير محدد (اختر منتج)`;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('يوجد أخطاء في النموذج:' + errorMsg);
            }
        });

        function addInvoiceRow() {
            const tbody = document.getElementById('invoiceItems');
            const newRow = `
                <tr class="invoice-row">
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-control product-select" required onchange="updatePrice(this, ${rowIndex})">
                            <option value="">اختر المنتج</option>
                            ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price_per_kg}">${p.name_ar} (${p.current_stock_kg} كج)</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][quantity_kg]" step="0.01" min="0.01" class="form-control quantity-input" required onchange="calculateRow(${rowIndex})">
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][unit_price]" step="0.01" class="form-control price-input" required onchange="calculateRow(${rowIndex})">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control total-input" readonly>
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

        function updatePrice(select, index) {
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const row = select.closest('tr');
            const priceInput = row.querySelector('.price-input');
            priceInput.value = price || 0;
            calculateRow(index);
        }

        function calculateRow(index) {
            const rows = document.querySelectorAll('.invoice-row');
            const row = rows[index];
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
            const tax = subtotal * 0.15;
            const total = subtotal + tax;

            document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ر.س';
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
    </script>
@endsection
