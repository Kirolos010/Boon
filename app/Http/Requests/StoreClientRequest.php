<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ($this->user()->isAdmin() || $this->user()->isSales());
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => 'اسم العميل مطلوب',
            'name_ar.string' => 'اسم العميل يجب أن يكون نصياً',
            'name_ar.max' => 'اسم العميل يجب ألا يتجاوز 255 حرفاً',

            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.string' => 'رقم الهاتف يجب أن يكون نصياً',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 أحرف',

            'address.string' => 'العنوان يجب أن يكون نصاً',
            'address.max' => 'العنوان يجب ألا يتجاوز 500 حرفاً',

            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرفاً',
        ];
    }
}
