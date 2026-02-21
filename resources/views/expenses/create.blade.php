@extends('layouts.app')

@section('title', 'إضافة مصروف')
@section('navbar-title', 'إضافة مصروف جديد')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- التصنيف -->
                    <x-form-group
                        name="expense_category_id"
                        label="التصنيف"
                        :required="true"
                    >
                        <select name="expense_category_id" id="expense_category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                            <option value="">-- اختر التصنيف --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-group>

                    <!-- المبلغ -->
                    <x-form-group
                        name="amount"
                        label="المبلغ (ريال)"
                        :required="true"
                    >
                        <input type="number" name="amount" id="amount"
                            value="{{ old('amount') }}"
                            min="0" step="0.01"
                            placeholder="0.00"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                    </x-form-group>

                    <!-- التاريخ -->
                    <x-form-group
                        name="expense_date"
                        label="تاريخ المصروف"
                        :required="true"
                    >
                        <input type="date" name="expense_date" id="expense_date"
                            value="{{ old('expense_date', date('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                    </x-form-group>

                    <!-- طريقة الدفع -->
                    <x-form-group
                        name="payment_method"
                        label="طريقة الدفع"
                        :required="true"
                    >
                        <select name="payment_method" id="payment_method"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>بطاقة</option>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        </select>
                    </x-form-group>

                    <!-- المستلم -->
                    <x-form-group
                        name="recipient"
                        label="المستلم (الجهة المدفوع لها)"
                    >
                        <input type="text" name="recipient" id="recipient"
                            value="{{ old('recipient') }}"
                            placeholder="اسم الشخص أو الجهة"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500">
                    </x-form-group>

                    <!-- رقم الإيصال -->
                    <x-form-group
                        name="receipt_number"
                        label="رقم الإيصال أو الفاتورة"
                    >
                        <input type="text" name="receipt_number" id="receipt_number"
                            value="{{ old('receipt_number') }}"
                            placeholder="رقم الإيصال إن وجد"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500">
                    </x-form-group>

                    <!-- الوصف -->
                    <x-form-group
                        name="description"
                        label="الوصف والتفاصيل"
                        :required="true"
                    >
                        <textarea name="description" id="description" rows="4"
                            placeholder="وصف تفصيلي للمصروف..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>{{ old('description') }}</textarea>
                    </x-form-group>

                    <!-- ملاحظات إضافية -->
                    <x-form-group
                        name="notes"
                        label="ملاحظات إضافية (اختياري)"
                    >
                        <textarea name="notes" id="notes" rows="2"
                            placeholder="أي ملاحظات أخرى..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500">{{ old('notes') }}</textarea>
                    </x-form-group>
                </div>

                <!-- أزرار الحفظ -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
                    <a href="{{ route('expenses.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="bg-coffee-600 hover:bg-coffee-700 text-white px-6 py-2 rounded-md">
                        حفظ المصروف
                    </button>
                </div>
            </form>
        </div>

        <!-- نصائح -->
        <div class="max-w-2xl mx-auto mt-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-900 mb-2">💡 نصائح سريعة:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• تأكد من اختيار التصنيف الصحيح للمصروف لسهولة التتبع</li>
                    <li>• احتفظ برقم الإيصال أو الفاتورة للرجوع إليه</li>
                    <li>• اكتب وصفاً تفصيلياً يسهل فهم المصروف لاحقاً</li>
                    <li>• يمكنك إضافة تصنيف جديد من قائمة التصنيفات إذا لزم الأمر</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
