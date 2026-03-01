@extends('layouts.app')

@section('title', 'بيع سريع جديد')
@section('navbar-title', 'بيع سريع جديد')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quick-sales.index') }}">البيع السريع</a></li>
            <li class="breadcrumb-item active">بيع جديد</li>
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

    <form method="POST" action="{{ route('quick-sales.store') }}" id="quickSaleForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <x-card>
                    @slot('header')
                        <i class="fas fa-bolt"></i> بيانات البيع السريع
                    @endslot

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="اسم العميل (اختياري)" name="customer_name">
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="أدخل اسم العميل (اختياري)">
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="sale_date" class="form-label"><strong>تاريخ ووقت البيع</strong></label>
                                <input type="datetime-local" name="sale_date" id="sale_date" value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}" class="form-control form-control-lg" required>
                                @error('sale_date')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary"></i> منتجات البيع</h5>
                        <button type="button" class="btn btn-sm btn-success" onclick="addSaleRow()">
                            <i class="fas fa-plus"></i> إضافة منتج
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

                    <table class="table table-bordered table-hover" id="saleItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">المنتج</th>
                                <th style="width: 15%;">الكمية (كج)</th>
                                <th style="width: 18%;">السعر</th>
                                <th style="width: 18%;">الإجمالي</th>
                                <th style="width: 14%;">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="saleItems">
                            <tr class="sale-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-control product-select searchable-select-sm" required onchange="updatePrice(this, 0)">
                                        <option value="">اختر المنتج</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-main-category-id="{{ $product->main_category_id }}"
                                                data-sub-category-id="{{ $product->sub_category_id }}"
                                                data-stock="{{ $product->currentStock() }}">
                                                {{ $product->name_ar }} ({{ $product->currentStock() }} كج)
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                        value="1" min="0.01" step="0.01" required onchange="calculateRow(0)">
                                </td>
                                <td>
                                    <input type="number" name="items[0][price]" class="form-control price-input"
                                        value="0" min="0" step="0.01" required onchange="calculateRow(0)">
                                </td>
                                <td>
                                    <input type="number" class="form-control total-input" value="0" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" onclick="removeRow(this)" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> حذف
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
                        <i class="fas fa-calculator"></i> ملخص البيع
                    @endslot

                    <div class="summary-row">
                        <span>الإجمالي الفرعي:</span>
                        <strong id="subtotalDisplay">0.00 ج.م</strong>
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

                    <hr>

                    <div class="form-group mb-3">
                        <label for="payment_method" class="form-label"><strong>طريقة الدفع</strong></label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="">اختر طريقة الدفع (افتراضي: نقدي)</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="notes" class="form-label"><strong>ملاحظات</strong></label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    </div>

                    <hr>

                    <div class="d-grid gap-2" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> حفظ البيع
                        </button>
                        <a href="{{ route('quick-sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة
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

        #saleItemsTable input,
        #saleItemsTable select {
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

        function buildProductOptionsHtml(items) {
            return items.map(product => (
                `<option value="${product.id}" data-price="${getProductPrice(product)}" data-stock="${product.current_stock_kg || 0}" data-main-category-id="${product.main_category_id || ''}" data-sub-category-id="${product.sub_category_id || ''}">${product.name_ar} (${product.current_stock_kg || 0} كج)</option>`
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
                    const row = select.closest('tr');
                    const priceInput = row.querySelector('.price-input');
                    const selected = select.options[select.selectedIndex];
                    priceInput.value = selected?.dataset?.price || 0;
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

        // Handle form submission
        document.getElementById('quickSaleForm').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.sale-row');
            let isValid = true;
            let errorMsg = '';

            rows.forEach((row, index) => {
                const productSelect = row.querySelector('[name*="product_id"]');
                const quantityInput = row.querySelector('[name*="quantity"]');
                const priceInput = row.querySelector('[name*="price"]');
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const stock = parseFloat(selectedOption.dataset.stock) || 0;
                const quantity = parseFloat(quantityInput.value) || 0;

                if (!productSelect.value) {
                    isValid = false;
                    errorMsg += `\n- سطر ${index + 1}: اختر منتج`;
                }
                if (!quantityInput.value || quantity <= 0) {
                    isValid = false;
                    errorMsg += `\n- سطر ${index + 1}: أدخل كمية صحيحة`;
                }
                if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
                    isValid = false;
                    errorMsg += `\n- سطر ${index + 1}: السعر غير محدد (اختر منتج)`;
                }
                if (quantity > stock) {
                    isValid = false;
                    errorMsg += `\n- سطر ${index + 1}: الكمية المطلوبة (${quantity}) أكبر من المخزون المتاح (${stock})`;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('يوجد أخطاء في النموذج:' + errorMsg);
            }
        });

        function addSaleRow() {
            const tbody = document.getElementById('saleItems');
            const filteredProducts = getFilteredProducts();
            const newRow = `
                <tr class="sale-row">
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-control product-select searchable-select-sm" required onchange="updatePrice(this, ${rowIndex})">
                            <option value="">اختر المنتج</option>
                            ${buildProductOptionsHtml(filteredProducts)}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][quantity]" class="form-control quantity-input" value="1" min="0.01" step="0.01" required onchange="calculateRow(${rowIndex})">
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][price]" class="form-control price-input" value="0" min="0" step="0.01" required onchange="calculateRow(${rowIndex})">
                    </td>
                    <td>
                        <input type="number" class="form-control total-input" value="0" readonly>
                    </td>
                    <td class="text-center">
                        <button type="button" onclick="removeRow(this)" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', newRow);
            initSearchableSelects(tbody.lastElementChild);
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
            const rows = document.querySelectorAll('.sale-row');
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

            // Tax disabled - commented out
            // const tax = subtotal * 0.15;
            // const total = subtotal + tax;

            // Using subtotal as total without tax
            const tax = 0;
            const total = subtotal;

            document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ج.م';
            // document.getElementById('taxDisplay').textContent = tax.toFixed(2) + ' ج.م';
            document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ج.م';
        }

        function removeRow(button) {
            const rows = document.querySelectorAll('.sale-row');
            if (rows.length > 1) {
                button.closest('tr').remove();
                calculateTotal();
            } else {
                alert('يجب أن يحتوي البيع على منتج واحد على الأقل');
            }
        }

        // Initial calculation
        document.addEventListener('DOMContentLoaded', function() {
            populateCategoryFilters();

            loadSelect2Assets(function () {
                initSearchableSelects();
            });

            refreshProductSelectOptions();
            calculateTotal();
        });
    </script>
@endsection
