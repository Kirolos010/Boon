<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - بون</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Cairo', 'Tajawal', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }

        body {
            background: linear-gradient(135deg, #faf8f5 0%, #f5f0eb 50%, #ede6df 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #f9f6f2 0%, #f0ebe4 100%);
            padding: 50px 40px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #2c2420;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            font-size: 14px;
            color: #8b7d72;
            margin: 12px 0 0 0;
            font-weight: 400;
        }

        .login-body {
            padding: 40px 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #2c2420;
            display: block;
            margin-bottom: 8px;
            text-transform: none;
            letter-spacing: 0;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            padding-right: 44px;
            font-size: 15px;
            border: 2px solid #e8dfd5;
            border-radius: 12px;
            background: #faf8f5;
            color: #2c2420;
            transition: all 0.3s ease;
            font-family: 'Cairo', sans-serif;
        }

        .form-control::placeholder {
            color: #a89888;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #c4a87d;
            background: white;
            box-shadow: 0 0 0 3px rgba(196, 168, 125, 0.1);
            transform: translateY(-1px);
        }

        .form-control:hover:not(:focus) {
            border-color: #dfd4ca;
            background: #fcfaf8;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a89888;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .form-control:focus ~ .input-icon,
        .form-control:not(:placeholder-shown) ~ .input-icon {
            color: #c4a87d;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            gap: 8px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #e8ddf5;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 0;
            margin-right: 0;
            transition: all 0.3s ease;
            accent-color: #c4a87d;
        }

        .form-check-input:hover {
            border-color: #c4a87d;
        }

        .form-check-input:checked {
            background-color: #c4a87d;
            border-color: #c4a87d;
        }

        .form-check-label {
            font-size: 14px;
            color: #2c2420;
            cursor: pointer;
            user-select: none;
            font-weight: 500;
            margin: 0;
        }

        .submit-btn {
            width: 100%;
            padding: 14px 24px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #c4a87d 0%, #b39568 100%);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: none;
            letter-spacing: 0;
            font-family: 'Cairo', sans-serif;
            box-shadow: 0 8px 16px rgba(196, 168, 125, 0.25);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(196, 168, 125, 0.35);
            background: linear-gradient(135deg, #b39568 0%, #a08456 100%);
        }

        .submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(196, 168, 125, 0.2);
        }

        .divider {
            margin: 28px 0;
            display: flex;
            align-items: center;
            color: #d4cac0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e8ddf5;
        }

        .divider span {
            padding: 0 12px;
            font-size: 13px;
            color: #a89888;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .footer-link a {
            font-size: 13px;
            color: #c4a87d;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .footer-link a:hover {
            color: #b39568;
            gap: 8px;
        }

        .signup-link {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ede6df;
            margin-top: 20px;
        }

        .signup-link p {
            font-size: 13px;
            color: #8b7d72;
            margin: 0;
        }

        .signup-link a {
            color: #c4a87d;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .signup-link a:hover {
            color: #b39568;
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-size: 14px;
            margin-bottom: 24px;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background-color: #ffe8e8;
            color: #d32f2f;
            border-left: 4px solid #d32f2f;
        }

        .alert-danger .btn-close {
            opacity: 0.5;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .test-credentials {
            background: #faf8f5;
            padding: 16px;
            border-radius: 12px;
            margin-top: 20px;
            border-left: 4px solid #c4a87d;
        }

        .test-credentials p {
            font-size: 12px;
            color: #8b7d72;
            margin: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .test-credentials code {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #2c2420;
            font-weight: 500;
            font-size: 11px;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #d32f2f;
            margin-top: 6px;
        }

        @media (max-width: 480px) {
            .login-card {
                max-width: 100%;
                border-radius: 16px;
            }

            .login-header {
                padding: 40px 30px;
            }

            .login-header h1 {
                font-size: 28px;
            }

            .login-body {
                padding: 30px;
            }

            .form-control {
                padding: 12px 14px;
                padding-right: 40px;
                font-size: 14px;
            }

            .submit-btn {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-coffee"></i> بون</h1>
                <p>نظام إدارة المبيعات والمخزون</p>
            </div>

            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <i class="fas fa-circle-exclamation" style="flex-shrink: 0; margin-top: 2px;"></i>
                            <div>
                                <strong>خطأ في تسجيل الدخول</strong>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <div class="input-group">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="أدخل بريدك الإلكتروني"
                                required
                                autofocus
                            >
                            <i class="fas fa-envelope input-icon"></i>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <div class="input-group">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="أدخل كلمة المرور"
                                required
                            >
                            <i class="fas fa-lock input-icon"></i>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="checkbox-wrapper">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="form-check-input"
                        >
                        <label for="remember" class="form-check-label">تذكرني</label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </button>
                </form>

                <!-- Forgot Password & Register Links -->
                {{-- <div class="footer-links">
                    @if (Route::has('password.request'))
                        <div class="footer-link">
                            <a href="{{ route('password.request') }}">
                                <span>نسيت كلمة المرور؟</span>
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    @endif
                </div> --}}

                <!-- Signup -->
                {{-- <div class="signup-link">
                    <p>
                        ليس لديك حساب؟
                        <a href="{{ route('register') }}">إنشاء حساب جديد</a>
                    </p>
                </div> --}}

                <!-- Test Credentials -->
                {{-- <div class="test-credentials">
                    <p><strong>🔑 بيانات اختبار:</strong></p>
                    <p><i class="fas fa-envelope" style="color: #c4a87d;"></i> البريد: <code>admin@boon.local</code></p>
                    <p><i class="fas fa-lock" style="color: #c4a87d;"></i> كلمة المرور: <code>password</code></p>
                </div> --}}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
