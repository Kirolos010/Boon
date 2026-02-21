@extends('layouts.app')

@section('title', 'تعديل المستخدم')
@section('navbar-title', 'تعديل المستخدم')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.users.index') }}">المستخدمين</a></li>
            <li class="breadcrumb-item active">تعديل</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تعديل المستخدم</h1>
            <p class="page-title-subtitle">تعديل بيانات المستخدم: {{ $user->name }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <x-card title="بيانات المستخدم">
        <form action="{{ route('settings.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name -->
            <x-form-group
                name="name"
                label="الاسم الكامل"
                placeholder="الاسم الكامل"
                value="{{ $user->name }}"
                required="true" />

            <!-- Email -->
            <x-form-group
                name="email"
                label="البريد الإلكتروني"
                placeholder="البريد الإلكتروني"
                type="email"
                value="{{ $user->email }}"
                required="true" />

            <!-- Role -->
            <div class="form-group">
                <label for="role_id" class="form-label">الدور <span style="color: #dc3545;">*</span></label>
                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                    <option value="">-- اختر --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) === (string)$role->id ? 'selected' : '' }}>
                            {{ $role->name_ar }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password (Optional for edit) -->
            <x-form-group
                name="password"
                label="كلمة المرور الجديدة (اتركها فارغة للعدم التغيير)"
                placeholder="كلمة المرور الجديدة"
                type="password"
                help="إذا كنت تروغب بتغيير كلمة المرور (8 أحرف على الأقل)" />

            <!-- Password Confirmation -->
            <x-form-group
                name="password_confirmation"
                label="تأكيد كلمة المرور الجديدة"
                placeholder="تأكيد كلمة المرور"
                type="password" />

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </x-card>
@endsection
