@extends('layouts.app')

@section('title', 'الفئات')
@section('navbar-title', 'إدارة الفئات')

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
            <h1 class="page-title">الفئات</h1>
            <p class="page-title-subtitle">إدارة فئات المنتجات الرئيسية والفرعية</p>
        </div>
    </div>

    <!-- Under Construction Alert -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-wrench"></i> <strong>قيد الإنشاء</strong>
        <br>هذه الصفحة قيد التطوير حالياً. سيتم إضافة المزيد من الميزات قريباً.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Placeholder Card -->
    <x-card>
        <div class="text-center py-5">
            <i class="fas fa-list" style="font-size: 48px; color: var(--coffee-light); opacity: 0.3;"></i>
            <h3 class="mt-3">الفئات والأقسام</h3>
            <p class="text-muted">هذه الميزة قيد التطوير</p>
            <p class="text-muted small">ستتمكن من إدارة الفئات الرئيسية والفرعية للمنتجات قريباً</p>
        </div>
    </x-card>
@endsection
