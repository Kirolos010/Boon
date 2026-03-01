@extends('layouts.app')

@section('title', 'الملف الشخصي')
@section('navbar-title', 'الملف الشخصي')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">الملف الشخصي</li>
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

    <div class="row">
        <div class="col-lg-8">
            <x-card>
                @slot('header')
                    <i class="fas fa-user"></i> معلومات الحساب
                @endslot

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>الاسم</strong></label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>البريد الإلكتروني</strong></label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>الدور</strong></label>
                            <input type="text" class="form-control" value="{{ $user->role->name_ar ?? 'مستخدم' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>تاريخ الإنشاء</strong></label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('Y-m-d H:i') }}" readonly>
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card>
                @slot('header')
                    <i class="fas fa-key"></i> الأمان
                @endslot

                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> لتغيير كلمة المرور الخاصة بك، اضغط على الزر أدناه:
                </div>

                <a href="{{ route('profile.change-password') }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> تغيير كلمة المرور
                </a>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card>
                @slot('header')
                    <i class="fas fa-circle-user"></i> صورة الملف الشخصي
                @endslot

                <div class="text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3" style="
                        width: 120px;
                        height: 120px;
                        background: linear-gradient(135deg, #8B7355, #D2B48C);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 48px;
                        font-weight: bold;
                    ">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <p class="mb-2"><strong>{{ $user->name }}</strong></p>
                    <small class="text-muted">{{ $user->email }}</small>
                </div>

                <div class="mb-3">
                    <p class="mb-1"><strong>الحالة:</strong></p>
                    <p><span class="badge bg-success">نشط</span></p>
                </div>

                <div>
                    <p class="mb-1"><strong>آخر دخول:</strong></p>
                    <p class="text-muted">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
            </x-card>
        </div>
    </div>
@endsection
