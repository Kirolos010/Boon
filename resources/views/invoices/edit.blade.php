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
                                <select name="client_id" id="client_id" class="form-select form-select-lg searchable-select-lg" required>
                                    <option value="">اختر العميل</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                            data-search="{{ strtolower(trim(($client->name_ar ?? '') . ' ' . ($client->name ?? '') . ' ' . ($client->phone ?? ''))) }}"
                                            {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name_ar }} - الرصيد: {{ number_format($client->balance, 2) }} ج.م
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_date" class="form-label"><strong>تاريخ ووقت الفاتورة *</strong></label>
                                <input type="datetime-local" name="invoice_date" id="invoice_date"
                                    value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d\TH:i')) }}"
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
                                    <select name="items[{{ $index }}][product_id]" class="form-control product-select searchable-select-sm" required onchange="updatePrice(this)">
                                        <option value="">اختر المنتج</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ $product->selling_price_per_kg }}"
                                                data-main-category-id="{{ $product->main_category_id }}"
                                                data-sub-category-id="{{ $product->sub_category_id }}"
                                                data-stock="{{ $product->currentStock() }}"
                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name_ar }} (متوفر: {{ $product->currentStock() }} كج)
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
                        <strong id="subtotalDisplay">{{ number_format($invoice->subtotal, 2) }} ج.م</strong>
                    </div>
                    <div class="summary-row">
                        <span>الخصم:</span>
                        <strong id="discountDisplay" class="text-success">{{ number_format($invoice->discount ?? 0, 2) }} ج.م</strong>
                    </div>
                    {{-- Tax disabled
                    <div class="summary-row">
                        <span>الضريبة (15%):</span>
                        <strong id="taxDisplay">{{ number_format($invoice->tax, 2) }} ج.م</strong>
                    </div>
                    --}}
                    <div class="summary-row border-top pt-2 mt-2">
                        <span class="fs-5">الإجمالي النهائي:</span>
                        <strong id="totalDisplay" class="fs-5 text-primary">{{ number_format($invoice->total, 2) }} ج.م</strong>
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
@endsection

@section('scripts')
    <script>
        let rowIndex = {{ count($invoice->items) }};
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

        function buildProductOptionsHtml(items) {
            return items.map(product => (
                `<option value="${product.id}" data-price="${getProductPrice(product)}" data-main-category-id="${product.main_category_id || ''}" data-sub-category-id="${product.sub_category_id || ''}" data-stock="${product.current_stock_kg || 0}">${product.name_ar} (${product.current_stock_kg || 0} كج)</option>`
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
            const $client = $('#client_id');
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
                    updatePrice(select);
                } else {
                    select.value = '';
                    const row = select.closest('tr');
                    const priceInput = row.querySelector('.price-input');
                    if (priceInput) {
                        priceInput.value = 0;
                    }
                }
            });

            calculateTotal();
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

        function addInvoiceRow() {
            const tbody = document.getElementById('invoiceItems');
            const filteredProducts = getFilteredProducts();
            const newRow = `
                <tr class="invoice-row">
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-control product-select searchable-select-sm" required onchange="updatePrice(this)">
                            <option value="">اختر المنتج</option>
                            ${buildProductOptionsHtml(filteredProducts)}
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
            initSearchableSelects(tbody.lastElementChild);
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
            populateCategoryFilters();

            loadSelect2Assets(function () {
                initSearchableSelects();
            });

            refreshProductSelectOptions();

            calculateTotal();

            // Add event listener for discount input
            document.getElementById('discount').addEventListener('input', calculateTotal);
        });
    </script>
@endsection
