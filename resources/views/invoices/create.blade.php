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
                                <select name="client_id" id="clientSelect" class="form-select form-select-lg searchable-select-lg" required>
                                    <option value="">اختر العميل</option>
                                    @if($clients && count($clients) > 0)
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" data-search="{{ strtolower(trim(($client->name_ar ?? '') . ' ' . ($client->name ?? '') . ' ' . ($client->phone ?? ''))) }}">
                                                {{ $client->name_ar }}@if($client->phone) - {{ $client->phone }}@endif
                                            </option>
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
                                <label for="invoice_date" class="form-label"><strong>تاريخ ووقت الفاتورة</strong></label>
                                <input type="datetime-local" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d\TH:i')) }}" class="form-control form-control-lg" required>
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

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label for="productMainCategoryFilter" class="form-label">القسم الرئيسي</label>
                            <select id="productMainCategoryFilter" class="form-select">
                                <option value="">كل الأقسام</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="productSubCategoryFilter" class="form-label">القسم الفرعي</label>
                            <select id="productSubCategoryFilter" class="form-select">
                                <option value="">كل الأقسام الفرعية</option>
                            </select>
                        </div>
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
                                    <select name="items[0][product_id]" class="form-control product-select searchable-select-sm" required onchange="updatePrice(this, 0)">
                                        <option value="">اختر المنتج</option>
                                        @if($products && count($products) > 0)
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price_per_kg }}" data-main-category-id="{{ $product->main_category_id }}" data-sub-category-id="{{ $product->sub_category_id }}">
                                                    {{ $product->name_ar }} ({{ $product->current_stock_kg }} كج)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity_kg]" value="1" step="0.01" min="0.01" class="form-control quantity-input" required onchange="calculateRow(0)">
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
                        <strong id="subtotalDisplay">0.00 ج.م</strong>
                    </div>
                    <div class="summary-row">
                        <span>الخصم:</span>
                        <strong id="discountDisplay">0.00 ج.م</strong>
                    </div>
                    {{-- Tax disabled
                    <div class="summary-row">
                        <span>الضريبة (15%):</span>
                        <strong id="taxDisplay">0.00 ج.م</strong>
                    </div>
                    --}}
                    <div class="summary-row" style="border-top: 2px solid var(--coffee-dark); padding-top: 10px; margin-top: 10px;">
                        <span style="font-size: 18px;">الإجمالي النهائي:</span>
                        <strong id="totalDisplay" style="font-size: 20px; color: var(--coffee-dark);">0.00 ج.م</strong>
                    </div>

                    <input type="hidden" name="total_amount" id="totalAmountInput">

                    <hr>

                    <div class="form-group mb-3">
                        <label for="payment_status" class="form-label"><strong>حالة الدفع</strong></label>
                        <select id="payment_status" class="form-select">
                            <option value="full">دفع المبلغ كامل</option>
                            <option value="partial">دفع جزء من المبلغ</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="amountPaidGroup">
                        <label for="amount_paid" class="form-label"><strong>المبلغ المدفوع</strong></label>
                        <input type="number" name="amount_paid" id="amount_paid" value="0" step="0.01" min="0" class="form-control" placeholder="0.00">
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
                    </div>

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

        .searchable-select-lg + .select2-container .select2-selection--single {
            height: calc(3.5rem + 2px) !important;
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
            padding: 0 0.75rem;
            display: flex;
            align-items: center;
        }

        .searchable-select-lg + .select2-container .select2-selection__rendered {
            line-height: normal;
            height: 100%;
            display: flex;
            align-items: center;
            padding-left: 0;
            padding-right: 0;
        }

        .searchable-select-lg + .select2-container .select2-selection__arrow {
            height: 100%;
            left: 10px;
            right: auto;
        }

        .searchable-select-sm + .select2-container .select2-selection--single {
            min-height: calc(2.25rem + 2px);
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.25rem 0.6rem;
            display: flex;
            align-items: center;
        }

        .searchable-select-sm + .select2-container .select2-selection__rendered {
            line-height: 1.3;
            padding-left: 0;
            padding-right: 0;
            font-size: 14px;
        }

        .searchable-select-sm + .select2-container .select2-selection__arrow {
            height: 100%;
            left: 8px;
            right: auto;
        }

        #invoiceItemsTable input,
        #invoiceItemsTable select {
            font-size: 14px;
        }
    </style>

    <script>
        let rowIndex = 1;
        const products = @json($products ?? []);
        const productFilters = {
            mainCategoryId: '',
            subCategoryId: '',
        };

        function normalizeText(value) {
            return (value || '').toString().toLowerCase().trim();
        }

        function getProductPrice(product) {
            return product.selling_price_per_kg ?? product.selling_price ?? 0;
        }

        function getProductLabel(product) {
            return `${product.name_ar} (${product.current_stock_kg} كج)`;
        }

        function buildProductOptionsHtml(items) {
            return items.map(product => (
                `<option value="${product.id}" data-price="${getProductPrice(product)}" data-main-category-id="${product.main_category_id || ''}" data-sub-category-id="${product.sub_category_id || ''}">${getProductLabel(product)}</option>`
            )).join('');
        }

        function getFilteredProducts() {
            return products.filter(product => {
                const matchesMain = !productFilters.mainCategoryId || String(product.main_category_id || '') === productFilters.mainCategoryId;
                const matchesSub = !productFilters.subCategoryId || String(product.sub_category_id || '') === productFilters.subCategoryId;
                return matchesMain && matchesSub;
            });
        }

        function loadSelect2Assets(callback) {
            if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
                callback();
                return;
            }

            if (!document.getElementById('select2-css')) {
                const link = document.createElement('link');
                link.id = 'select2-css';
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
                document.head.appendChild(link);
            }

            if (document.getElementById('select2-js')) {
                document.getElementById('select2-js').addEventListener('load', callback, { once: true });
                return;
            }

            const script = document.createElement('script');
            script.id = 'select2-js';
            script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            script.onload = callback;
            document.body.appendChild(script);
        }

        function initSearchableSelects(context = document) {
            if (!(window.jQuery && window.jQuery.fn && window.jQuery.fn.select2)) return;

            const $ = window.jQuery;
            const $client = $('#clientSelect');
            if ($client.length) {
                if ($client.hasClass('select2-hidden-accessible')) {
                    $client.select2('destroy');
                }
                $client.select2({
                    width: '100%',
                    dir: 'rtl',
                    placeholder: 'اختر العميل',
                });
            }

            const $products = $(context).find('.product-select');
            $products.each(function () {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    width: '100%',
                    dir: 'rtl',
                    placeholder: 'اختر المنتج',
                });
            });
        }

        function refreshProductSelectOptions() {
            const filtered = getFilteredProducts();
            const optionsHtml = `<option value="">اختر المنتج</option>${buildProductOptionsHtml(filtered)}`;

            document.querySelectorAll('.product-select').forEach(select => {
                const previousValue = select.value;
                select.innerHTML = optionsHtml;

                if (previousValue && filtered.some(p => String(p.id) === String(previousValue))) {
                    select.value = previousValue;
                } else {
                    select.value = '';
                }

                const row = select.closest('tr');
                const priceInput = row.querySelector('.price-input');
                if (!select.value && priceInput) {
                    priceInput.value = 0;
                }
            });

            document.querySelectorAll('.invoice-row').forEach((_, index) => calculateRow(index));

            initSearchableSelects();
        }

        function populateCategoryFilters() {
            const mainSelect = document.getElementById('productMainCategoryFilter');
            const subSelect = document.getElementById('productSubCategoryFilter');

            const mainMap = new Map();
            const subMap = new Map();

            products.forEach(product => {
                if (product.main_category_id) {
                    const mainName = product.main_category?.name_ar || product.main_category?.name || `قسم ${product.main_category_id}`;
                    mainMap.set(String(product.main_category_id), mainName);
                }

                if (product.sub_category_id) {
                    const subName = product.sub_category?.name_ar || product.sub_category?.name || `فرعي ${product.sub_category_id}`;
                    subMap.set(String(product.sub_category_id), {
                        name: subName,
                        mainId: String(product.main_category_id || ''),
                    });
                }
            });

            const mainOptions = [...mainMap.entries()]
                .sort((a, b) => a[1].localeCompare(b[1], 'ar'))
                .map(([id, name]) => `<option value="${id}">${name}</option>`)
                .join('');
            mainSelect.innerHTML = `<option value="">كل الأقسام</option>${mainOptions}`;

            function renderSubOptions() {
                const options = [...subMap.entries()]
                    .filter(([_, item]) => !productFilters.mainCategoryId || item.mainId === productFilters.mainCategoryId)
                    .sort((a, b) => a[1].name.localeCompare(b[1].name, 'ar'))
                    .map(([id, item]) => `<option value="${id}">${item.name}</option>`)
                    .join('');
                subSelect.innerHTML = `<option value="">كل الأقسام الفرعية</option>${options}`;

                if (productFilters.subCategoryId && !subMap.has(productFilters.subCategoryId)) {
                    productFilters.subCategoryId = '';
                    subSelect.value = '';
                }
            }

            renderSubOptions();

            mainSelect.addEventListener('change', function () {
                productFilters.mainCategoryId = this.value;
                productFilters.subCategoryId = '';
                renderSubOptions();
                refreshProductSelectOptions();
            });

            subSelect.addEventListener('change', function () {
                productFilters.subCategoryId = this.value;
                refreshProductSelectOptions();
            });
        }

        // Handle form submission
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.invoice-row');
            let isValid = true;
            let errorMsg = '';

            rows.forEach((row, index) => {
                const productSelect = row.querySelector('[name*="product_id"]');
                const quantityInput = row.querySelector('[name*="quantity_kg"]');
                const priceInput = row.querySelector('[name*="unit_price"]');

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
            const filteredProducts = getFilteredProducts();
            const newRow = `
                <tr class="invoice-row">
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-control product-select searchable-select-sm" required onchange="updatePrice(this, ${rowIndex})">
                            <option value="">اختر المنتج</option>
                            ${buildProductOptionsHtml(filteredProducts)}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][quantity_kg]" value="1" step="0.01" min="0.01" class="form-control quantity-input" required onchange="calculateRow(${rowIndex})">
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
            initSearchableSelects(tbody.lastElementChild);
            calculateRow(rowIndex);
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

            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const afterDiscount = Math.max(0, subtotal - discount);
            // Tax disabled - commented out
            // const tax = afterDiscount * 0.15;
            // const total = afterDiscount + tax;

            // Using afterDiscount as total without tax
            const tax = 0;
            const total = afterDiscount;

            document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ج.م';
            document.getElementById('discountDisplay').textContent = discount.toFixed(2) + ' ج.م';
            // document.getElementById('taxDisplay').textContent = tax.toFixed(2) + ' ج.م';
            document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ج.م';
            document.getElementById('totalAmountInput').value = total.toFixed(2);

            // Update max amount for payment
            const amountPaidInput = document.getElementById('amount_paid');
            if (amountPaidInput) {
                amountPaidInput.max = total.toFixed(2);

                const paymentStatus = document.getElementById('payment_status')?.value;
                if (paymentStatus === 'full') {
                    amountPaidInput.value = total.toFixed(2);
                }
            }
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

        // Add event listener for discount field to recalculate on change
        document.getElementById('discount').addEventListener('input', calculateTotal);

        document.getElementById('payment_status').addEventListener('change', function () {
            const amountPaidGroup = document.getElementById('amountPaidGroup');
            const amountPaidInput = document.getElementById('amount_paid');
            const total = parseFloat(document.getElementById('totalAmountInput').value) || 0;

            if (this.value === 'partial') {
                // Show the input for partial payment
                amountPaidGroup.style.display = 'block';
                amountPaidInput.value = '';
                amountPaidInput.max = total.toFixed(2);
                amountPaidInput.required = true;
            } else {
                // For full payment - keep visible but set value
                amountPaidGroup.style.display = 'block';
                amountPaidInput.value = total.toFixed(2);
                amountPaidInput.max = total.toFixed(2);
                amountPaidInput.required = false;
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            populateCategoryFilters();

            loadSelect2Assets(function () {
                initSearchableSelects();
            });

            refreshProductSelectOptions();

            const firstProduct = document.querySelector('.product-select');
            if (firstProduct && firstProduct.value) {
                updatePrice(firstProduct, 0);
            } else {
                calculateTotal();
            }

            const paymentStatus = document.getElementById('payment_status');
            if (paymentStatus) {
                paymentStatus.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection
