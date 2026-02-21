<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h1>إعادة تعيين كلمة المرور</h1>
            <p>أدخل كلمة المرور الجديدة</p>
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

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group @error('email') error @enderror">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group @error('password') error @enderror">
                    <label for="password">كلمة المرور الجديدة</label>
                    <input type="password" id="password" name="password" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group @error('password_confirmation') error @enderror">
                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="form-button">تعيين كلمة المرور</button>
            </form>
        </div>
    </div>
</x-guest-layout>
