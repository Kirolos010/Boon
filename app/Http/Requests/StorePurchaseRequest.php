<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ($this->user()->isAdmin() || $this->user()->isSales());
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_kg' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'المورد مطلوب',
            'supplier_id.exists' => 'المورد المحدد غير موجود',

            'items.required' => 'يجب إضافة عنصر واحد على الأقل',
            'items.array' => 'العناصر يجب أن تكون قائمة',
            'items.min' => 'يجب إضافة عنصر واحد على الأقل',

            'items.*.product_id.required' => 'المنتج مطلوب لكل عنصر',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود',

            'items.*.quantity_kg.required' => 'الكمية مطلوبة لكل عنصر',
            'items.*.quantity_kg.numeric' => 'الكمية يجب أن تكون رقماً',
            'items.*.quantity_kg.min' => 'الكمية يجب أن تكون أكبر من صفر',

            'items.*.unit_price.required' => 'سعر الوحدة مطلوب لكل عنصر',
            'items.*.unit_price.numeric' => 'سعر الوحدة يجب أن يكون رقماً',
            'items.*.unit_price.min' => 'سعر الوحدة يجب أن يكون موجباً أو صفر',

            'tax.numeric' => 'الضريبة يجب أن تكون رقماً',
            'tax.min' => 'الضريبة يجب أن تكون موجبة أو صفر',

            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرفاً',
        ];
    }
}
