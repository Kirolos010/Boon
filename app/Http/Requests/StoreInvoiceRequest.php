<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSales());
    }

    public function rules(): array
    {
        return [
            'client_id' => 'nullable|exists:clients,id',
            'invoice_date' => 'nullable|date_format:Y-m-d\TH:i|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_kg' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,check,transfer,other',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_date.date_format' => 'صيغة التاريخ والوقت غير صحيحة',
            'invoice_date.date' => 'التاريخ والوقت غير صحيح',

            'items.required' => 'يجب إضافة عنصر واحد على الأقل',
            'items.array' => 'العناصر يجب أن تكون قائمة',
            'items.min' => 'يجب إضافة عنصر واحد على الأقل',

            'items.*.product_id.required' => 'المنتج مطلوب لكل عنصر',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود',

            'items.*.quantity_kg.required' => 'الكمية مطلوبة لكل عنصر',
            'items.*.quantity_kg.numeric' => 'الكمية يجب أن تكون رقماً',
            'items.*.quantity_kg.min' => 'الكمية يجب أن تكون أكبر من صفر',

            'client_id.exists' => 'العميل المحدد غير موجود',

            'discount.numeric' => 'الخصم يجب أن يكون رقماً',
            'discount.min' => 'الخصم يجب أن يكون موجباً أو صفر',

            'tax.numeric' => 'الضريبة يجب أن تكون رقماً',
            'tax.min' => 'الضريبة يجب أن تكون موجبة أو صفر',

            'amount_paid.numeric' => 'المبلغ المدفوع يجب أن يكون رقماً',
            'amount_paid.min' => 'المبلغ المدفوع يجب أن يكون موجباً أو صفر',

            'payment_method.in' => 'طريقة الدفع غير صحيحة',

            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرفاً',
        ];
    }
}
