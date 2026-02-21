@extends('layouts.app')

@section('title', 'مستخدم جديد')
@section('navbar-title', 'مستخدم جديد')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.users.index') }}">المستخدمين</a></li>
            <li class="breadcrumb-item active">مستخدم جديد</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">مستخدم جديد</h1>
            <p class="page-title-subtitle">إضافة مستخدم جديد للنظام</p>
        </div>
    </div>

    <!-- Form Card -->
    <x-card title="بيانات المستخدم">
        <form action="{{ route('settings.users.store') }}" method="POST">
            @csrf

            <!-- Name -->
            <x-form-group
                name="name"
                label="الاسم الكامل"
                placeholder="الاسم الكامل"
                required="true" />

            <!-- Email -->
            <x-form-group
                name="email"
                label="البريد الإلكتروني"
                placeholder="البريد الإلكتروني"
                type="email"
                required="true" />

            <!-- Role -->
            <div class="form-group">
                <label for="role_id" class="form-label">الدور <span style="color: #dc3545;">*</span></label>
                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                    <option value="">-- اختر --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') === (string)$role->id ? 'selected' : '' }}>
                            {{ $role->name_ar }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <x-form-group
                name="password"
                label="كلمة المرور"
                placeholder="كلمة المرور"
                type="password"
                required="true"
                help="يجب أن تكون كلمة المرور قوية (8 أحرف على الأقل)" />

            <!-- Password Confirmation -->
            <x-form-group
                name="password_confirmation"
                label="تأكيد كلمة المرور"
                placeholder="تأكيد كلمة المرور"
                type="password"
                required="true" />

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </x-card>
@endsection
