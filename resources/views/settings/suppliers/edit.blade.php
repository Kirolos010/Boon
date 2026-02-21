@extends('layouts.app')

@section('title', 'تعديل المورد')
@section('navbar-title', 'تعديل المورد')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.suppliers.index') }}">الموردين</a></li>
            <li class="breadcrumb-item active">تعديل</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تعديل المورد</h1>
            <p class="page-title-subtitle">تعديل بيانات المورد: {{ $supplier->name_ar ?? $supplier->name_en }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <x-card title="بيانات المورد">
        <form action="{{ route('settings.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name AR -->
            <x-form-group
                name="name_ar"
                label="الاسم بالعربي"
                placeholder="الاسم بالعربي"
                value="{{ $supplier->name_ar }}"
                required="true" />

            <!-- Name EN -->
            <x-form-group
                name="name_en"
                label="الاسم بالإنجليزي"
                placeholder="الاسم بالإنجليزي"
                value="{{ $supplier->name_en }}" />

            <!-- Email -->
            <x-form-group
                name="email"
                label="البريد الإلكتروني"
                placeholder="البريد الإلكتروني"
                type="email"
                value="{{ $supplier->email }}"
                required="true" />

            <!-- Phone -->
            <x-form-group
                name="phone"
                label="الهاتف"
                placeholder="رقم الهاتف"
                type="tel"
                value="{{ $supplier->phone }}"
                required="true" />

            <!-- Address -->
            <x-form-group
                name="address"
                label="العنوان"
                placeholder="العنوان"
                value="{{ $supplier->address }}" />

            <!-- City -->
            <x-form-group
                name="city"
                label="المدينة"
                placeholder="المدينة"
                value="{{ $supplier->city }}" />

            <!-- Country -->
            <x-form-group
                name="country"
                label="البلد"
                placeholder="البلد"
                value="{{ $supplier->country }}" />

            <!-- Payment Terms -->
            <x-form-group
                name="payment_terms"
                label="شروط الدفع"
                type="select"
                value="{{ $supplier->payment_terms }}"
                :options="['immediate' => 'فوري', 'net_30' => '30 يوم', 'net_60' => '60 يوم', 'net_90' => '90 يوم']" />

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
                <a href="{{ route('settings.suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </x-card>
@endsection
