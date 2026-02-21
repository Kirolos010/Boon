<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuickSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ($this->user()->isAdmin() || $this->user()->isSales());
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_kg' => 'required|numeric|min:0.01',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,check,transfer,other',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'يجب إضافة عنصر واحد على الأقل',
            'items.array' => 'العناصر يجب أن تكون قائمة',
            'items.min' => 'يجب إضافة عنصر واحد على الأقل',

            'items.*.product_id.required' => 'المنتج مطلوب لكل عنصر',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود',

            'items.*.quantity_kg.required' => 'الكمية مطلوبة لكل عنصر',
            'items.*.quantity_kg.numeric' => 'الكمية يجب أن تكون رقماً',
            'items.*.quantity_kg.min' => 'الكمية يجب أن تكون أكبر من صفر',

            'discount.numeric' => 'الخصم يجب أن يكون رقماً',
            'discount.min' => 'الخصم يجب أن يكون موجباً أو صفر',

            'tax.numeric' => 'الضريبة يجب أن تكون رقماً',
            'tax.min' => 'الضريبة يجب أن تكون موجبة أو صفر',

            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',

            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرفاً',
        ];
    }
}
