<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->route('user')?->id ?? 'NULL'),
            'role_id' => 'required|exists:roles,id',
        ];

        // إذا كان create فكلمة المرور مطلوبة
        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        // إذا كان update والموجود كلمة مرور جديدة
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            if ($this->filled('password')) {
                $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'هذا البريد موجود بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير صحيح',
            'role_id.required' => 'يجب اختيار دور المستخدم',
        ];
    }
}
