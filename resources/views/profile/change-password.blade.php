@extends('layouts.app')

@section('title', 'تغيير كلمة المرور')
@section('navbar-title', 'تغيير كلمة المرور')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">الملف الشخصي</a></li>
            <li class="breadcrumb-item active">تغيير كلمة المرور</li>
        </ol>
    </nav>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> خطأ:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-card>
                @slot('header')
                    <i class="fas fa-lock"></i> تغيير كلمة المرور
                @endslot

                <form method="POST" action="{{ route('profile.update-password') }}">
                    @csrf

                    <div class="form-group mb-4">
                        <label for="current_password" class="form-label"><strong>كلمة المرور الحالية *</strong></label>
                        <input type="password" name="current_password" id="current_password"
                            class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                            required placeholder="أدخل كلمة المرور الحالية">
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="form-label"><strong>كلمة المرور الجديدة *</strong></label>
                        <input type="password" name="password" id="password"
                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                            required placeholder="أدخل كلمة مرور قوية (8 أحرف على الأقل)">
                        <small class="form-text text-muted d-block mt-1">
                            <i class="fas fa-info-circle"></i> يجب أن تحتوي على 8 أحرف على الأقل
                        </small>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password_confirmation" class="form-label"><strong>تأكيد كلمة المرور *</strong></label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                            required placeholder="أعد إدخال كلمة المرور الجديدة">
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-warning"></i> <strong>تنبيه:</strong> سيتم تسجيل خروجك من جميع الأجهزة الأخرى بعد تغيير كلمة المرور.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> تحديث كلمة المرور
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-times-circle"></i> إلغاء
                        </a>
                    </div>
                </form>
            </x-card>

            <div class="alert alert-info mt-4" role="alert">
                <h6 class="alert-heading"><i class="fas fa-shield-alt"></i> نصائح الأمان:</h6>
                <ul class="mb-0">
                    <li>استخدم كلمة مرور قوية تحتوي على أحرف وأرقام</li>
                    <li>لا تشارك كلمة المرور مع أحد</li>
                    <li>قم بتغيير كلمة المرور بشكل دوري</li>
                    <li>تجنب استخدام كلمات سهلة التخمين</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
