<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h1>تأكيد كلمة المرور</h1>
            <p>أدخل كلمة المرور الخاصة بك للمتابعة</p>
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

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-group @error('password') error @enderror">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" required autofocus>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="form-button">تأكيد</button>
            </form>

            <div class="auth-footer">
                <p>أو <a href="{{ route('logout') }}" onclick="document.getElementById('logout-form').submit(); return false;">تسجيل الخروج</a></p>
            </div>
        </div>
    </div>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
        @csrf
    </form>
</x-guest-layout>
