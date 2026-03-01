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
            'sale_date' => 'nullable|date_format:Y-m-d\TH:i|date',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,bank_transfer',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'sale_date.date_format' => 'صيغة التاريخ والوقت غير صحيحة',
            'sale_date.date' => 'تاريخ البيع غير صحيح',

            'customer_name.string' => 'اسم العميل يجب أن يكون نصاً',
            'customer_name.max' => 'اسم العميل يجب ألا يتجاوز 255 حرفاً',

            'items.required' => 'يجب إضافة عنصر واحد على الأقل',
            'items.array' => 'العناصر يجب أن تكون قائمة',
            'items.min' => 'يجب إضافة عنصر واحد على الأقل',

            'items.*.product_id.required' => 'المنتج مطلوب لكل عنصر',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود',

            'items.*.quantity.required' => 'الكمية مطلوبة لكل عنصر',
            'items.*.quantity.numeric' => 'الكمية يجب أن تكون رقماً',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',

            'items.*.price.required' => 'السعر مطلوب لكل عنصر',
            'items.*.price.numeric' => 'السعر يجب أن يكون رقماً',
            'items.*.price.min' => 'السعر يجب أن يكون موجباً أو صفر',

            'payment_method.in' => 'طريقة الدفع غير صحيحة',

            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرفاً',
        ];
    }
}
