@extends('layouts.app')

@section('title', 'الفئات الرئيسية')
@section('navbar-title', 'إدارة الفئات الرئيسية')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الفئات</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">الفئات الرئيسية</h1>
            <p class="page-title-subtitle">إدارة فئات المنتجات</p>
        </div>
        <a href="{{ route('settings.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> فئة جديدة
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" icon="check-circle" />
    @endif
    @if(session('error'))
        <x-alert message="{{ session('error') }}" type="danger" icon="exclamation-circle" />
    @endif

    <!-- Categories Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr style="background-color: var(--coffee-dark); color: white;">
                        <th style="width: 40%;">الفئة الرئيسية</th>
                        <th style="width: 20%;">عدد الفئات الفرعية</th>
                        <th style="width: 20%;">عدد المنتجات</th>
                        <th style="width: 20%;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mainCategories as $category)
                        <tr>
                            <td>
                                <a href="{{ route('settings.categories.subcategories', $category) }}" class="text-decoration-none" style="color: var(--coffee-dark); font-weight: 600;">
                                    <i class="fas fa-folder" style="color: var(--coffee-medium); margin-left: 8px;"></i>
                                    {{ $category->name_ar }}
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background-color: var(--coffee-medium);">
                                    {{ $category->subCategories->count() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $category->products->count() }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('settings.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('settings.categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟ سيتم حذف جميع الفئات الفرعية أيضاً')">
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
                            <td colspan="4" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا توجد فئات</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mainCategories->hasPages())
            {{ $mainCategories->links() }}
        @endif
    </x-card>

    <style>
        a:hover {
            text-decoration: underline !important;
        }
    </style>
@endsection
