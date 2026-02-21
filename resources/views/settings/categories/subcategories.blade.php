@extends('layouts.app')

@section('title', 'الفئات الفرعية - ' . $category->name_ar)
@section('navbar-title', 'إدارة الفئات الفرعية')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.categories.index') }}">الفئات الرئيسية</a></li>
            <li class="breadcrumb-item active">{{ $category->name_ar }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $category->name_ar }}</h1>
            <p class="page-title-subtitle">الفئات الفرعية لـ: <strong>{{ $category->name_ar }}</strong></p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">
            <i class="fas fa-plus"></i> فئة فرعية جديدة
        </button>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" icon="check-circle" />
    @endif
    @if(session('error'))
        <x-alert message="{{ session('error') }}" type="danger" icon="exclamation-circle" />
    @endif

    <!-- Search Bar -->
    <x-card>
        <form method="GET" action="{{ route('settings.categories.subcategories', $category) }}" class="row g-3 mb-0">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="ابحث عن فئة فرعية..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> بحث
                </button>
            </div>
            @if(request('search'))
                <div class="col-md-3">
                    <a href="{{ route('settings.categories.subcategories', $category) }}" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> حذف
                    </a>
                </div>
            @endif
        </form>
    </x-card>

    <!-- Sub-Categories Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr style="background-color: var(--coffee-dark); color: white;">
                        <th style="width: 60%;">اسم الفئة الفرعية</th>
                        <th style="width: 20%;">عدد المنتجات</th>
                        <th style="width: 20%;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subcategories as $subCategory)
                        <tr>
                            <td>
                                <i class="fas fa-cube" style="color: var(--coffee-medium); margin-left: 8px;"></i>
                                {{ $subCategory->name_ar }}
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $subCategory->products->count() }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editSubcategoryModal"
                                        data-subcategory-id="{{ $subCategory->id }}"
                                        data-subcategory-name="{{ $subCategory->name_ar }}"
                                        title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('settings.categories.destroySubcategory', [$category, $subCategory]) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة الفرعية؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">
                                    @if(request('search'))
                                        لا توجد فئات فرعية تطابق البحث
                                    @else
                                        لا توجد فئات فرعية
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subcategories->hasPages())
            {{ $subcategories->links() }}
        @endif
    </x-card>

    <!-- Add Sub-Category Modal -->
    <div class="modal fade" id="addSubcategoryModal" tabindex="-1" aria-labelledby="addSubcategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubcategoryModalLabel">إضافة فئة فرعية جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <form action="{{ route('settings.categories.storeSubcategory', $category) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <x-form-group
                            label="اسم الفئة الفرعية"
                            :errors="$errors"
                            name="name_ar"
                            placeholder="أدخل اسم الفئة الفرعية"
                            required
                        />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Sub-Category Modal -->
    <div class="modal fade" id="editSubcategoryModal" tabindex="-1" aria-labelledby="editSubcategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubcategoryModalLabel">تعديل الفئة الفرعية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <x-form-group
                            label="اسم الفئة الفرعية"
                            :errors="$errors"
                            name="name_ar"
                            id="editSubcategoryName"
                            placeholder="أدخل اسم الفئة الفرعية"
                            required
                        />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle edit modal
        const editSubcategoryModal = document.getElementById('editSubcategoryModal');
        editSubcategoryModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const subcategoryId = button.getAttribute('data-subcategory-id');
            const subcategoryName = button.getAttribute('data-subcategory-name');

            // Update the form action
            const form = document.getElementById('editForm');
            form.action = `{{ route('settings.categories.updateSubcategory', [$category, ':id']) }}`.replace(':id', subcategoryId);

            // Update the input value
            document.getElementById('editSubcategoryName').value = subcategoryName;
        });
    </script>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header > div h1 {
            color: var(--coffee-dark);
            margin-bottom: 5px;
        }

        .page-header > div p {
            color: #666;
            font-size: 14px;
        }

        .modal-header {
            background-color: var(--coffee-light);
            color: white;
            border-color: var(--coffee-dark);
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-info {
            background-color: #e3f2fd;
            color: #1976d2;
        }
    </style>
@endsection
