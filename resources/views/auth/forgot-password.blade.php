<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h1>نسيت كلمة المرور؟</h1>
            <p>أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين</p>
        </div>

        <div class="auth-body">
            @if ($errors->any())
                <div class="alert alert-error">
                    <strong>خطأ</strong>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group @error('email') error @enderror">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="form-button">إرسال رابط إعادة التعيين</button>
            </form>

            <div class="auth-footer">
                <p><a href="{{ route('login') }}">عودة إلى تسجيل الدخول</a></p>
            </div>
        </div>
    </div>
</x-guest-layout>
