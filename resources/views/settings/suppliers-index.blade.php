@extends('layouts.app')

@section('title', 'الموردون')
@section('navbar-title', 'إدارة الموردين')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الموردون</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">الموردون</h1>
            <p class="page-title-subtitle">إدارة موردي المنتجات</p>
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
            <i class="fas fa-truck" style="font-size: 48px; color: var(--coffee-light); opacity: 0.3;"></i>
            <h3 class="mt-3">إدارة الموردين</h3>
            <p class="text-muted">هذه الميزة قيد التطوير</p>
            <p class="text-muted small">ستتمكن من إدارة الموردين ومعلوماتهم قريباً</p>
        </div>
    </x-card>
@endsection
