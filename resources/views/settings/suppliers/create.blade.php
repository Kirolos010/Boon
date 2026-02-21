@extends('layouts.app')

@section('title', 'مورد جديد')
@section('navbar-title', 'مورد جديد')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.suppliers.index') }}">الموردين</a></li>
            <li class="breadcrumb-item active">مورد جديد</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">مورد جديد</h1>
            <p class="page-title-subtitle">إضافة مورد جديد</p>
        </div>
    </div>

    <!-- Form Card -->
    <x-card title="بيانات المورد">
        <form action="{{ route('settings.suppliers.store') }}" method="POST">
            @csrf

            <!-- Name AR -->
            <x-form-group
                name="name_ar"
                label="الاسم بالعربي"
                placeholder="الاسم بالعربي"
                required="true" />

            <!-- Name EN -->
            <x-form-group
                name="name_en"
                label="الاسم بالإنجليزي"
                placeholder="الاسم بالإنجليزي" />

            <!-- Email -->
            <x-form-group
                name="email"
                label="البريد الإلكتروني"
                placeholder="البريد الإلكتروني"
                type="email"
                required="true" />

            <!-- Phone -->
            <x-form-group
                name="phone"
                label="الهاتف"
                placeholder="رقم الهاتف"
                type="tel"
                required="true" />

            <!-- Address -->
            <x-form-group
                name="address"
                label="العنوان"
                placeholder="العنوان" />

            <!-- City -->
            <x-form-group
                name="city"
                label="المدينة"
                placeholder="المدينة" />

            <!-- Country -->
            <x-form-group
                name="country"
                label="البلد"
                placeholder="البلد" />

            <!-- Payment Terms -->
            <x-form-group
                name="payment_terms"
                label="شروط الدفع"
                type="select"
                :options="['immediate' => 'فوري', 'net_30' => '30 يوم', 'net_60' => '60 يوم', 'net_90' => '90 يوم']" />

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('settings.suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </x-card>
@endsection
