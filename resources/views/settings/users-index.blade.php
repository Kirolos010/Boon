@extends('layouts.app')

@section('title', 'إدارة المستخدمين')
@section('navbar-title', 'إدارة المستخدمين')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">إدارة المستخدمين</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">إدارة المستخدمين</h1>
            <p class="page-title-subtitle">إدارة حسابات المستخدمين للنظام</p>
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
            <i class="fas fa-users" style="font-size: 48px; color: var(--coffee-light); opacity: 0.3;"></i>
            <h3 class="mt-3">إدارة المستخدمين</h3>
            <p class="text-muted">هذه الميزة قيد التطوير</p>
            <p class="text-muted small">ستتمكن من إدارة حسابات المستخدمين والصلاحيات قريباً</p>
        </div>
    </x-card>
@endsection
