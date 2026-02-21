@extends('layouts.app')

@section('title', 'تعديل الفئة')
@section('navbar-title', 'تعديل الفئة')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.categories.index') }}">الفئات</a></li>
            <li class="breadcrumb-item active">تعديل</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تعديل الفئة</h1>
            <p class="page-title-subtitle">تعديل بيانات الفئة: {{ $mainCategory->name_ar }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <x-card title="بيانات الفئة">
        <form action="{{ route('settings.categories.update', $mainCategory) }}" method="POST" id="categoryForm">
            @csrf
            @method('PUT')

            <!-- Main Category Name AR -->
            <div class="mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-folder"></i> الفئة الرئيسية
                </h5>
                <x-form-group
                    name="name_ar"
                    label="اسم الفئة"
                    placeholder="أدخل اسم الفئة"
                    value="{{ $mainCategory->name_ar }}"
                    required="true" />
            </div>

            <!-- Sub-Categories Section -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>
                        <i class="fas fa-list"></i> الفئات الفرعية
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addSubcategoryBtn">
                        <i class="fas fa-plus"></i> إضافة فئة فرعية
                    </button>
                </div>

                <div id="subcategoriesContainer">
                    <!-- Subcategories will be populated -->
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
                <a href="{{ route('settings.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </x-card>

    <style>
        .subcategory-item {
            background-color: var(--cream-light);
            border-left: 3px solid var(--coffee-medium);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .subcategory-input-wrapper {
            flex: 1;
        }

        .subcategory-input-wrapper input {
            width: 100%;
        }

        .subcategory-remove-btn {
            margin-top: 32px;
        }
    </style>

    <script>
        let subcategoryCount = 0;
        const existingSubcategories = @json($mainCategory->subCategories ?? []);

        document.getElementById('addSubcategoryBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addSubcategoryField();
        });

        function addSubcategoryField(name_ar = '') {
            const container = document.getElementById('subcategoriesContainer');
            const index = subcategoryCount++;

            const html = `
                <div class="subcategory-item" id="subcategory-${index}">
                    <div class="subcategory-input-wrapper">
                        <input
                            type="text"
                            class="form-control"
                            name="subcategories[${index}][name_ar]"
                            placeholder="اسم الفئة الفرعية"
                            value="${name_ar}"
                            autocomplete="off">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger subcategory-remove-btn" onclick="removeSubcategory('subcategory-${index}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
        }

        function removeSubcategory(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.remove();
            }
        }

        // Load existing subcategories on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (existingSubcategories.length > 0) {
                existingSubcategories.forEach(sub => {
                    addSubcategoryField(sub.name_ar);
                });
            } else {
                addSubcategoryField();
            }
        });
    </script>
@endsection
