@extends('layouts.app')

@section('title', 'تفاصيل المنتج: ' . $product->name_ar)
@section('navbar-title', 'تفاصيل المنتج')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
            <li class="breadcrumb-item active">{{ $product->name_ar }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $product->name_ar }}</h1>
            <p class="page-title-subtitle">كود المنتج: <code>{{ $product->sku }}</code></p>
        </div>
        <div>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> عودة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <x-card>
                @slot('header')
                    <i class="fas fa-info-circle"></i> معلومات المنتج الأساسية
                @endslot

                <div class="row">
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>اسم المنتج</label>
                            <h5 class="text-coffee-dark">{{ $product->name_ar }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>كود المنتج (SKU)</label>
                            <h5><code>{{ $product->sku }}</code></h5>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>الفئة الرئيسية</label>
                            <p>{{ $product->mainCategory->name_ar ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>الفئة الفرعية</label>
                            <p>{{ $product->subCategory->name_ar ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>سعر الشراء (لكل كج)</label>
                            <p class="text-primary font-weight-bold">{{ number_format($product->purchase_price_per_kg, 2) }} ج.م</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>سعر البيع (لكل كج)</label>
                            <p class="text-success font-weight-bold">{{ number_format($product->selling_price_per_kg, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="product-info-item">
                            <label>الربح لكل كج</label>
                            <p class="text-info font-weight-bold">{{ number_format($product->selling_price_per_kg - $product->purchase_price_per_kg, 2) }} ج.م</p>
                        </div>
                    </div>
                </div>

                @if($product->notes)
                    <hr>
                    <div class="product-info-item">
                        <label>ملاحظات</label>
                        <p class="text-muted">{{ $product->notes }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Stock Information -->
            <x-card>
                @slot('header')
                    <i class="fas fa-warehouse"></i> معلومات المخزون
                @endslot

                <div class="row">
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>الكمية الحالية</label>
                            <h4 class="text-info">{{ $product->current_stock_kg }} كج</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <label>الحد الأدنى للتنبيه</label>
                            <h4>{{ $product->minimum_stock_alert }} كج</h4>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="product-info-item">
                    <label>حالة المخزون</label>
                    @if($product->current_stock_kg > $product->minimum_stock_alert)
                        <span class="badge badge-success" style="padding: 8px 12px; font-size: 14px;">
                            <i class="fas fa-check-circle"></i> متوفر بكمية كافية
                        </span>
                        <p class="text-success mt-2">المخزون كافي ولا يوجد نقص</p>
                    @elseif($product->current_stock_kg > 0)
                        <span class="badge badge-warning" style="padding: 8px 12px; font-size: 14px;">
                            <i class="fas fa-exclamation-triangle"></i> حد أدنى
                        </span>
                        <p class="text-warning mt-2">المخزون قريب من الانتهاء - ننصح بالطلب</p>
                    @else
                        <span class="badge badge-danger" style="padding: 8px 12px; font-size: 14px;">
                            <i class="fas fa-times-circle"></i> نفد
                        </span>
                        <p class="text-danger mt-2">المخزون انتهى - طلب فوري مطلوب</p>
                    @endif
                </div>

                <hr>

                <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                    <i class="fas fa-exchange-alt"></i> تعديل المخزون
                </button>
            </x-card>

            <!-- Stock Movements -->
            <x-card>
                @slot('header')
                    <i class="fas fa-history"></i> سجل حركات المخزون
                @endslot

                @if($product->stockMovements && $product->stockMovements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr style="background-color: var(--coffee-light); color: white;">
                                    <th>النوع</th>
                                    <th>الكمية (كج)</th>
                                    <th>الملاحظات</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->stockMovements as $movement)
                                    <tr>
                                        <td>
                                            @if($movement->type === 'in')
                                                <span class="badge badge-success">دخول</span>
                                            @else
                                                <span class="badge badge-danger">خروج</span>
                                            @endif
                                        </td>
                                        <td>{{ $movement->quantity_kg }}</td>
                                        <td>{{ $movement->notes ?? '-' }}</td>
                                        <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">لا توجد حركات مخزون</p>
                @endif
            </x-card>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            {{-- <!-- Supplier Information -->
            @if($product->supplier)
                <x-card>
                    @slot('header')
                        <i class="fas fa-truck"></i> المورد
                    @endslot

                    <p><strong>{{ $product->supplier->name_ar ?? $product->supplier->name }}</strong></p>
                    @if($product->supplier->phone)
                        <p><i class="fas fa-phone"></i> {{ $product->supplier->phone }}</p>
                    @endif
                    @if($product->supplier->email)
                        <p><i class="fas fa-envelope"></i> {{ $product->supplier->email }}</p>
                    @endif
                </x-card>
            @endif --}}

            <!-- Statistics -->
            <x-card>
                @slot('header')
                    <i class="fas fa-chart-bar"></i> الإحصائيات
                @endslot

                <div class="stat-item mb-3">
                    <label>إجمالي المبيعات</label>
                    <h5>{{ $details['total_sold'] ?? 0 }} كج</h5>
                </div>

                <hr>

                <div class="stat-item mb-3">
                    <label>إجمالي المشتريات</label>
                    <h5>{{ $details['total_purchased'] ?? 0 }} كج</h5>
                </div>

                <hr>

                <div class="stat-item">
                    <label>نسبة الربح</label>
                    <h5 class="text-success">{{ $details['profit_margin'] ?? 0 }}%</h5>
                </div>
            </x-card>

            <!-- Created Info -->
            <x-card>
                @slot('header')
                    <i class="fas fa-clock"></i> معلومات الإنشاء
                @endslot

                <p>
                    <strong>أنشئ بواسطة:</strong><br>
                    {{ $product->creator->name ?? 'Unknown' }}
                </p>
                <p>
                    <strong>بتاريخ:</strong><br>
                    {{ $product->created_at->format('Y-m-d H:i') }}
                </p>
                <p>
                    <strong>آخر تحديث:</strong><br>
                    {{ $product->updated_at->format('Y-m-d H:i') }}
                </p>
            </x-card>
        </div>
    </div>

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
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleQuantitySign()">
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
                                استخدم الزر الأخضر لإضافة (+) أو الأحمر لطرح (-)
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

    <style>
        .product-info-item {
            margin-bottom: 15px;
        }

        .product-info-item label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            text-align: right;
            display: block;
        }

        .product-info-item h5,
        .product-info-item p {
            margin: 5px 0 0 0;
        }

        .text-coffee-dark {
            color: var(--coffee-dark);
        }

        .stat-item label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
        }
    </style>

    @section('scripts')
        <script>
            let isAddMode = true;

            function toggleQuantitySign() {
                isAddMode = !isAddMode;
                const sign = document.getElementById('quantitySign');
                const button = document.querySelector('[onclick="toggleQuantitySign()"]');

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
            document.querySelector('form').addEventListener('submit', function(e) {
                const quantityInput = document.getElementById('quantity');
                if (!isAddMode) {
                    quantityInput.value = -Math.abs(quantityInput.value);
                } else {
                    quantityInput.value = Math.abs(quantityInput.value);
                }
            });
        </script>
    @endsection
@endsection
