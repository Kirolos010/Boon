<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ($this->user()->isAdmin() || $this->user()->isSales());
    }

    public function rules(): array
    {
        $routeProduct = $this->route('product');
        $productId = is_object($routeProduct) ? ($routeProduct->id ?? null) : $routeProduct;

        return [
            'name_ar' => 'required|string|max:255',
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')
                    ->ignore($productId)
                    ->whereNull('deleted_at')
            ],
            'main_category_id' => 'required|exists:main_categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price_per_kg' => 'required|numeric|min:0',
            'selling_price_per_kg' => 'required|numeric|min:0',
            'minimum_stock_alert' => 'required|numeric|min:0',
            'current_stock_kg' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => 'اسم المنتج مطلوب',
            'name_ar.string' => 'اسم المنتج يجب أن يكون نصياً',
            'name_ar.max' => 'اسم المنتج يجب ألا يتجاوز 255 حرفاً',

            'sku.string' => 'كود المنتج يجب أن يكون نصياً',
            'sku.max' => 'كود المنتج يجب ألا يتجاوز 100 حرفاً',
            'sku.unique' => 'كود المنتج موجود بالفعل',

            'main_category_id.required' => 'القسم الرئيسي مطلوب',
            'main_category_id.exists' => 'القسم الرئيسي غير صحيح',

            'sub_category_id.required' => 'القسم الفرعي مطلوب',
            'sub_category_id.exists' => 'القسم الفرعي غير صحيح',

            'supplier_id.exists' => 'المورد غير صحيح',

            'purchase_price_per_kg.required' => 'سعر الشراء لكل كيلوجرام مطلوب',
            'purchase_price_per_kg.numeric' => 'سعر الشراء يجب أن يكون رقماً',
            'purchase_price_per_kg.min' => 'سعر الشراء يجب أن يكون موجباً أو صفر',

            'selling_price_per_kg.required' => 'سعر البيع لكل كيلوجرام مطلوب',
            'selling_price_per_kg.numeric' => 'سعر البيع يجب أن يكون رقماً',
            'selling_price_per_kg.min' => 'سعر البيع يجب أن يكون موجباً أو صفر',

            'minimum_stock_alert.required' => 'الحد الأدنى للمخزون مطلوب',
            'minimum_stock_alert.numeric' => 'الحد الأدنى للمخزون يجب أن يكون رقماً',
            'minimum_stock_alert.min' => 'الحد الأدنى للمخزون يجب أن يكون موجباً أو صفر',

            'current_stock_kg.numeric' => 'الكمية الحالية يجب أن تكون رقماً',
            'current_stock_kg.min' => 'الكمية الحالية يجب أن تكون موجبة أو صفر',

            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرفاً',
        ];
    }
}
