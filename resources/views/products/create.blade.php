@extends('layouts.app')

@section('title', isset($product) ? 'تعديل المنتج' : 'إضافة منتج جديد')
@section('navbar-title', isset($product) ? 'تعديل المنتج' : 'إضافة منتج')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
            <li class="breadcrumb-item active">{{ isset($product) ? 'تعديل' : 'إضافة' }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">{{ isset($product) ? 'تعديل المنتج' : 'إضافة منتج جديد' }}</h1>
    </div>

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

    <x-card>
        <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST">
            @csrf
            @if(isset($product))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <x-form-group
                        type="text"
                        name="name_ar"
                        label="اسم المنتج"
                        placeholder="قهوة عربية"
                        value="{{ $product->name_ar ?? '' }}"
                        required />
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sku" class="form-label">كود المنتج (SKU)</label>
                        <input type="text" id="sku" name="sku" class="form-control bg-light" value="{{ $product->sku ?? '' }}" readonly style="background-color: #f8f9fa;" />
                        <small class="form-text text-muted">يتم توليده تلقائياً</small>
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
                        value="{{ isset($product) && $product->subCategory ? $product->subCategory->main_category_id : '' }}"
                        id="main_category_id"
                        required />
                </div>
                <div class="col-md-6">
                    <x-form-group
                        type="select"
                        name="sub_category_id"
                        label="القسم الفرعي"
                        :options="[]"
                        value="{{ $product->sub_category_id ?? '' }}"
                        id="sub_category_id"
                        required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-form-group
                        type="number"
                        name="purchase_price_per_kg"
                        label="سعر الشراء لكل كيلو"
                        placeholder="50.00"
                        value="{{ $product->purchase_price_per_kg ?? '' }}"
                        step="0.01"
                        required />
                </div>
                <div class="col-md-6">
                    <x-form-group
                        type="number"
                        name="selling_price_per_kg"
                        label="سعر البيع لكل كيلو"
                        placeholder="75.00"
                        value="{{ $product->selling_price_per_kg ?? '' }}"
                        step="0.01"
                        required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-form-group
                        type="number"
                        name="minimum_stock_alert"
                        label="الحد الأدنى للمخزون (كج)"
                        placeholder="10"
                        value="{{ $product->minimum_stock_alert ?? '' }}"
                        step="0.01"
                        required />
                </div>
                <div class="col-md-4">
                    <x-form-group
                        type="number"
                        name="current_stock_kg"
                        label="الكمية الحالية (كج)"
                        placeholder="50"
                        value="{{ $product->current_stock_kg ?? '' }}"
                        step="0.01" />
                </div>
                {{-- <div class="col-md-4">
                    <x-form-group
                        type="select"
                        name="supplier_id"
                        label="المورد"
                        :options="$suppliers ?? []"
                        value="{{ $product->supplier_id ?? '' }}" />
                </div> --}}
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-form-group
                        type="textarea"
                        name="notes"
                        label="ملاحظات"
                        placeholder="أضف أي ملاحظات إضافية عن المنتج..."
                        value="{{ $product->notes ?? '' }}"
                        rows="3" />
                </div>
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i>
                    {{ isset($product) ? 'تحديث المنتج' : 'إضافة المنتج' }}
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </x-card>
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

            // Get initial subcategories if editing
            @if(isset($product) && $product->subCategory)
                console.log('Loading initial subcategories for editing');
                loadSubcategories({{ $product->subCategory->main_category_id }}, {{ $product->sub_category_id }});
            @endif

            // Generate SKU when product name changes (only for new products)
            @if(!isset($product))
                const nameInput = document.querySelector('[name="name_ar"]');
                if (nameInput) {
                    nameInput.addEventListener('blur', function() {
                        if (this.value.trim()) {
                            generateSku();
                        }
                    });
                }
            @endif
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

        function generateSku() {
            const productName = document.querySelector('[name="name_ar"]').value.trim();
            if (!productName) return;

            fetch(`/api/products/sku/${encodeURIComponent(productName)}`)
                .then(response => response.json())
                .then(data => {
                    console.log('SKU generated:', data);
                    if (data.status === 'success') {
                        document.getElementById('sku').value = data.sku;
                    }
                })
                .catch(error => console.error('خطأ في توليد الكود:', error));
        }
    </script>
@endsection
