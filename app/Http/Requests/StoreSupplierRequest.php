<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . ($this->route('supplier')?->id ?? 'NULL'),
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'nullable|string|in:cash,credit,bank_transfer',
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => 'اسم المورد (عربي) مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'هذا البريد موجود بالفعل',
            'phone.required' => 'رقم الهاتف مطلوب',
        ];
    }
}
