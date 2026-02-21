@extends('layouts.app')

@section('title', 'فئة جديدة')
@section('navbar-title', 'فئة جديدة')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.categories.index') }}">الفئات</a></li>
            <li class="breadcrumb-item active">فئة جديدة</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">فئة جديدة</h1>
            <p class="page-title-subtitle">إضافة فئة رئيسية جديدة مع فئات فرعية</p>
        </div>
    </div>

    <!-- Form Card -->
    <x-card title="بيانات الفئة">
        <form action="{{ route('settings.categories.store') }}" method="POST" id="categoryForm">
            @csrf

            <!-- Main Category Name AR -->
            <div class="mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-folder"></i> الفئة الرئيسية
                </h5>
                <x-form-group
                    name="name_ar"
                    label="اسم الفئة"
                    placeholder="أدخل اسم الفئة"
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
                    <!-- Subcategories will be added here dynamically -->
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
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

        // إضافة subcategory جديد عند الضغط على الزر
        document.getElementById('addSubcategoryBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addSubcategoryField();
        });

        function addSubcategoryField() {
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

        // إضافة أول subcategory field عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            addSubcategoryField();

            // معالجة الفورم عند الحفظ
            document.getElementById('categoryForm').addEventListener('submit', function(e) {
                // التحقق من أن name_ar موجود
                const categoryName = document.querySelector('input[name="name_ar"]').value.trim();
                if (!categoryName) {
                    e.preventDefault();
                    alert('يرجى إدخال اسم الفئة');
                    return false;
                }
            });
        });
    </script>
@endsection
