@extends('layouts.app')

@section('title', 'تعديل المنتج')
@section('navbar-title', 'تعديل المنتج')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
            <li class="breadcrumb-item active">تعديل</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">تعديل المنتج: {{ $product->name_ar }}</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <x-card>
                @slot('header')
                    <i class="fas fa-edit"></i> معلومات المنتج
                @endslot

                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group
                                type="text"
                                name="name_ar"
                                label="اسم المنتج"
                                value="{{ $product->name_ar }}"
                                required />
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku" class="form-label">كود المنتج (SKU)</label>
                                <input type="text" id="sku" class="form-control" value="{{ $product->sku }}" readonly />
                                <small class="form-text text-muted">لا يمكن تغييره</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group
                                type="select"
                                name="main_category_id"
                                label="القسم الرئيسي"
                                :options="collect($mainCategories ?? [])->mapWithKeys(function($cat) { return [$cat->id => $cat->name_ar]; })->toArray()"
                                value="{{ $product->subCategory->main_category_id }}"
                                id="main_category_id"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-form-group
                                type="select"
                                name="sub_category_id"
                                label="القسم الفرعي"
                                :options="[]"
                                value="{{ $product->sub_category_id }}"
                                id="sub_category_id"
                                required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group
                                type="number"
                                name="purchase_price_per_kg"
                                label="سعر الشراء"
                                value="{{ $product->purchase_price_per_kg }}"
                                step="0.01"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-form-group
                                type="number"
                                name="selling_price_per_kg"
                                label="سعر البيع"
                                value="{{ $product->selling_price_per_kg }}"
                                step="0.01"
                                required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <x-form-group
                                type="number"
                                name="minimum_stock_alert"
                                label="الحد الأدنى (كج)"
                                value="{{ $product->minimum_stock_alert }}"
                                step="0.01"
                                required />
                        </div>
                        <div class="col-md-4">
                            <x-form-group
                                type="number"
                                name="current_stock_kg"
                                label="الكمية الحالية (كج)"
                                value="{{ $product->current_stock_kg }}"
                                step="0.01" />
                        </div>
                        {{-- <div class="col-md-4">
                            <x-form-group
                                type="select"
                                name="supplier_id"
                                label="المورد"
                                :options="$suppliers ?? []"
                                value="{{ $product->supplier_id }}" />
                        </div> --}}
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <x-form-group
                                type="textarea"
                                name="notes"
                                label="ملاحظات"
                                value="{{ $product->notes }}"
                                rows="3" />
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> تحديث المنتج
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-md-4">
            <!-- Product Stats -->
            <x-card>
                @slot('header')
                    <i class="fas fa-info-circle"></i> معلومات المنتج
                @endslot

                <p><strong>الفئة:</strong> {{ $product->subCategory->name_ar ?? 'N/A' }}</p>
                <p><strong>الكود:</strong> <code>{{ $product->sku }}</code></p>
                <p><strong>الكمية:</strong> {{ $product->current_stock_kg }} كج</p>
                <p><strong>الحالة:</strong>
                    @if($product->current_stock_kg > $product->minimum_stock_alert)
                        <span class="badge badge-success">متوفر</span>
                    @elseif($product->current_stock_kg > 0)
                        <span class="badge badge-warning">حد أدنى</span>
                    @else
                        <span class="badge badge-danger">نفد</span>
                    @endif
                </p>
                <p><strong>الربح حسب الكج:</strong>
                    {{ number_format($product->selling_price_per_kg - $product->purchase_price_per_kg, 2) }} ج.م
                </p>
            </x-card>

            <!-- Actions -->
            <x-card>
                @slot('header')
                    <i class="fas fa-wrench"></i> إجراءات
                @endslot

                <a href="{{ route('products.show', $product) }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-eye"></i> عرض التفاصيل
                </a>
                <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                    <i class="fas fa-balance-scale"></i> تعديل المخزون
                </button>
                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash"></i> حذف المنتج
                    </button>
                </form>
            </x-card>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            const mainCategorySelect = document.getElementById('main_category_id');
            const subCategorySelect = document.getElementById('sub_category_id');

            if (!mainCategorySelect) {
                console.error('main_category_id element not found');
                return;
            }

            // Load subcategories when main category changes
            mainCategorySelect.addEventListener('change', function() {
                const mainCategoryId = this.value;
                console.log('Main category changed to:', mainCategoryId);
                if (mainCategoryId) {
                    loadSubcategories(mainCategoryId);
                } else {
                    subCategorySelect.innerHTML = '<option value="">-- اختر قسماً فرعياً --</option>';
                }
            });

            // Load initial subcategories on page load
            const initialMainCategoryId = {{ $product->subCategory->main_category_id }};
            const initialSubCategoryId = {{ $product->sub_category_id }};
            if (initialMainCategoryId) {
                loadSubcategories(initialMainCategoryId, initialSubCategoryId);
            }
        });

        function loadSubcategories(mainCategoryId, selectedSubcategoryId = null) {
            const subCategorySelect = document.getElementById('sub_category_id');
            subCategorySelect.innerHTML = '<option value="">جاري التحميل...</option>';
            subCategorySelect.disabled = true;

            fetch(`/api/products/subcategories/${mainCategoryId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Subcategories data:', data);
                    subCategorySelect.innerHTML = '<option value="">-- اختر قسماً فرعياً --</option>';

                    if (data.status === 'success' && data.data && data.data.length > 0) {
                        data.data.forEach(subcat => {
                            const option = document.createElement('option');
                            option.value = subcat.id;
                            option.textContent = subcat.name_ar;
                            if (selectedSubcategoryId && subcat.id == selectedSubcategoryId) {
                                option.selected = true;
                            }
                            subCategorySelect.appendChild(option);
                        });
                    } else {
                        subCategorySelect.innerHTML = '<option value="">لا توجد فئات فرعية</option>';
                    }
                    subCategorySelect.disabled = false;
                })
                .catch(error => {
                    console.error('خطأ:', error);
                    subCategorySelect.innerHTML = '<option value="">خطأ في التحميل</option>';
                    subCategorySelect.disabled = false;
                });
        }

        function adjustStock() {
            alert('سيتم فتح نافذة تعديل المخزون');
        }
    </script>

    <!-- Adjust Stock Modal -->
    <div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adjustStockLabel">تعديل المخزون</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('products.adjust-stock', $product) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> المخزون الحالي: <strong>{{ $product->current_stock_kg }} كج</strong>
                        </div>

                        <div class="form-group mb-3">
                            <label for="quantity" class="form-label">الكمية (كج) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <button type="button" class="btn btn-success" id="addBtn" onclick="toggleQuantitySign()">
                                    <i class="fas fa-plus" id="quantitySign"></i>
                                </button>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="quantity"
                                    id="quantity"
                                    placeholder="أدخل الكمية"
                                    step="0.01"
                                    required
                                />
                            </div>
                            <small class="form-text text-muted">
                                اضغط على الزر لتبديل بين الإضافة (+) والطرح (-)
                            </small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="reference" class="form-label">نوع الحركة <span class="text-danger">*</span></label>
                            <select class="form-select" name="reference" id="reference" required>
                                <option value="">-- اختر نوع الحركة --</option>
                                <option value="adjustment">تعديل يدوي</option>
                                <option value="purchase">شراء</option>
                                <option value="sales">مبيعات</option>
                                <option value="inventory">جرد</option>
                                <option value="return">استرجاع</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التعديل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let isAddMode = true;

        function toggleQuantitySign() {
            isAddMode = !isAddMode;
            const sign = document.getElementById('quantitySign');
            const button = document.getElementById('addBtn');

            if (isAddMode) {
                sign.classList.remove('fa-minus');
                sign.classList.add('fa-plus');
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');
            } else {
                sign.classList.remove('fa-plus');
                sign.classList.add('fa-minus');
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
            }
        }

        // Adjust quantity sign based on form submission
        const adjustStockForm = document.querySelector('#adjustStockModal form');
        if (adjustStockForm) {
            adjustStockForm.addEventListener('submit', function(e) {
                const quantityInput = document.getElementById('quantity');
                if (!isAddMode) {
                    quantityInput.value = -Math.abs(quantityInput.value);
                } else {
                    quantityInput.value = Math.abs(quantityInput.value);
                }
            });
        }
    </script>
@endsection
