<x-guest-layout>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f5e6d3 0%, #e8dcc8 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <!-- Card Header -->
                    <div class="card shadow-lg border-0" style="border-radius: 15px; overflow: hidden;">
                        <div style="background: linear-gradient(135deg, #8B6F47 0%, #6B5438 100%); color: white; padding: 40px 20px; text-align: center;">
                            <h2 style="margin: 0; font-weight: bold; font-size: 28px;">📝 إنشاء حساب جديد</h2>
                            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">انضم إلى نظام إدارة مبيعات ومخزون بون</p>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-5">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-circle"></i> خطأ!</strong>
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- Name Field -->
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-600"><strong>الاسم الكامل</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-user" style="color: #8B6F47;"></i></span>
                                        <input type="text"
                                               id="name"
                                               name="name"
                                               class="form-control form-control-lg border-0 @error('name') is-invalid @enderror"
                                               value="{{ old('name') }}"
                                               placeholder="أدخل اسمك الكامل"
                                               required
                                               autofocus>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email Field -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-600"><strong>البريد الإلكتروني</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-envelope" style="color: #8B6F47;"></i></span>
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               class="form-control form-control-lg border-0 @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="أدخل بريدك الإلكتروني"
                                               required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password Field -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-600"><strong>كلمة المرور</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-lock" style="color: #8B6F47;"></i></span>
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="form-control form-control-lg border-0 @error('password') is-invalid @enderror"
                                               placeholder="أدخل كلمة مرور قوية (8 أحرف على الأقل)"
                                               required>
                                        @error('password')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password Confirmation -->
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label fw-600"><strong>تأكيد كلمة المرور</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-lock" style="color: #8B6F47;"></i></span>
                                        <input type="password"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               class="form-control form-control-lg border-0 @error('password_confirmation') is-invalid @enderror"
                                               placeholder="أعد إدخال كلمة المرور"
                                               required>
                                        @error('password_confirmation')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Register Button -->
                                <button type="submit" class="btn btn-lg w-100 fw-bold" style="background: linear-gradient(135deg, #8B6F47 0%, #6B5438 100%); color: white; border: none; padding: 12px; border-radius: 8px; transition: all 0.3s;">
                                    <i class="fas fa-user-plus"></i> إنشاء الحساب
                                </button>
                            </form>

                            <hr class="my-4">

                            <!-- Login Link -->
                            <div class="text-center">
                                <small style="color: #666;">هل لديك حساب بالفعل؟
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: #8B6F47;">
                                        سجل دخولك
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
