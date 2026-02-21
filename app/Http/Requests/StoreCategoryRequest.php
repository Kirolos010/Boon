<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255|unique:main_categories,name_ar,' . ($this->route('category')?->id ?? 'NULL'),
            'subcategories' => 'nullable|array',
            'subcategories.*.name_ar' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => 'اسم الفئة مطلوب',
            'name_ar.unique' => 'هذا الاسم موجود بالفعل',
            'subcategories.*.name_ar.required' => 'اسم الفئة الفرعية مطلوب',
        ];
    }
}
