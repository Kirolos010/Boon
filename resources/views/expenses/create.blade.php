@extends('layouts.app')

@section('title', 'إضافة نفقة جديدة')
@section('navbar-title', 'إضافة نفقة جديدة')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">إضافة نفقة جديدة</h1>
            <p class="page-title-subtitle">تسجيل مصروف أو نفقة تشغيلية</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card>
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- التصنيف -->
                        <div class="col-md-6">
                            <label for="expense_category_id" class="form-label">الفئة <span class="text-danger">*</span></label>
                            <select name="expense_category_id" id="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror" required>
                                <option value="">-- اختر الفئة --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- المبلغ -->
                        <div class="col-md-6">
                            <label for="amount" class="form-label">المبلغ (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   min="0.01"
                                   step="0.01"
                                   placeholder="0.00"
                                   required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- التاريخ -->
                        <div class="col-md-6">
                            <label for="expense_date" class="form-label">تاريخ النفقة <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" id="expense_date"
                                   class="form-control @error('expense_date') is-invalid @enderror"
                                   value="{{ old('expense_date', date('Y-m-d')) }}"
                                   required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- رقم المرجع -->
                        <div class="col-md-6">
                            <label for="reference" class="form-label">رقم المرجع أو الإيصال</label>
                            <input type="text" name="reference" id="reference"
                                   class="form-control @error('reference') is-invalid @enderror"
                                   value="{{ old('reference') }}"
                                   placeholder="مثال: INV-001">
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

<!-- الوصف -->
                        <div class="col-12">
                            <label for="description" class="form-label">الوصف <span class="text-danger">*</span></label>
                            <textarea name="description" id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4"
                                      placeholder="وصف تفصيلي للنفقة..."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ملاحظات -->
                        <div class="col-12">
                            <label for="notes" class="form-label">ملاحظات إضافية</label>
                            <textarea name="notes" id="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="2"
                                      placeholder="ملاحظات اختيارية...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- أزرار الحفظ -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ النفقة
                        </button>
                    </div>
                </form>
            </x-card>

            <!-- نصائح -->
            <div class="alert alert-info mt-3">
                <h6 class="alert-heading"><i class="fas fa-lightbulb"></i> نصائح سريعة</h6>
                <ul class="mb-0 small">
                    <li>تأكد من اختيار الفئة الصحيحة للنفقة لسهولة التتبع والتقارير</li>
                    <li>احتفظ برقم المرجع أو الإيصال للرجوع إليه عند الحاجة</li>
                    <li>اكتب وصفاً واضحاً يسهل فهم طبيعة النفقة</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
