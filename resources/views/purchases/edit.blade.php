@extends('layouts.app')

@section('title', 'تعديل أمر شراء')
@section('navbar-title', 'تعديل أمر شراء رقم ' . $purchase->purchase_number)

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- المورد -->
                    <x-form-group
                        name="supplier_id"
                        label="المورد"
                        :required="true"
                    >
                        <select name="supplier_id" id="supplier_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                            <option value="">-- اختر المورد --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name_ar }} - {{ $supplier->phone }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-group>

                    <!-- تاريخ الطلب -->
                    <x-form-group
                        name="purchase_date"
                        label="تاريخ الطلب"
                        :required="true"
                    >
                        <input type="date" name="purchase_date" id="purchase_date"
                            value="{{ old('purchase_date', $purchase->purchase_date) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                    </x-form-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- تاريخ الاستلام المتوقع -->
                    <x-form-group
                        name="expected_delivery_date"
                        label="تاريخ الاستلام المتوقع"
                    >
                        <input type="date" name="expected_delivery_date" id="expected_delivery_date"
                            value="{{ old('expected_delivery_date', $purchase->expected_delivery_date) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500">
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
                            <option value="cash" {{ old('payment_method', $purchase->payment_method) === 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="credit" {{ old('payment_method', $purchase->payment_method) === 'credit' ? 'selected' : '' }}>آجل</option>
                            <option value="bank_transfer" {{ old('payment_method', $purchase->payment_method) === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        </select>
                    </x-form-group>
                </div>

                <!-- منتجات الطلب -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">منتجات الطلب</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">سعر الشراء</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجمالي</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseItems" class="bg-white divide-y divide-gray-200">
                                @foreach($purchase->items as $index => $item)
                                <tr class="purchase-row">
                                    <td class="px-4 py-3">
                                        <select name="items[{{ $index }}][product_id]"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 product-select"
                                            required onchange="updateCost(this)">
                                            <option value="">-- اختر المنتج --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-cost="{{ $product->cost_price }}"
                                                    data-stock="{{ $product->currentStock() }}"
                                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name_ar }} (مخزون حالي: {{ $product->currentStock() }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[{{ $index }}][quantity]"
                                            value="{{ $item->quantity }}"
                                            min="1" step="1"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 quantity-input"
                                            required onchange="calculateRow(this)">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[{{ $index }}][cost_price]"
                                            value="{{ $item->cost_price }}"
                                            min="0" step="0.01"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 cost-input"
                                            required onchange="calculateRow(this)">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number"
                                            value="{{ $item->quantity * $item->cost_price }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 total-input"
                                            readonly>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" onclick="removeRow(this)"
                                            class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="button" onclick="addPurchaseRow()"
                        class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                        + إضافة منتج
                    </button>
                </div>

                <!-- الملاحظات -->
                <div class="mb-6">
                    <x-form-group name="notes" label="ملاحظات (اختياري)">
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500">{{ old('notes', $purchase->notes) }}</textarea>
                    </x-form-group>
                </div>

                <!-- الملخص المالي -->
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg p-6 mb-6 border border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-900 mb-4">ملخص أمر الشراء</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2">
                            <span class="text-gray-700 font-medium">المجموع الفرعي:</span>
                            <span id="subtotalDisplay" class="font-bold text-lg text-gray-900">{{ number_format($purchase->subtotal, 2) }} ريال</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-purple-300">
                            <span class="text-gray-700 font-medium">الضريبة (15%):</span>
                            <span id="taxDisplay" class="font-bold text-lg text-gray-900">{{ number_format($purchase->tax, 2) }} ريال</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-gray-900 font-bold text-xl">إجمالي التكلفة:</span>
                            <span id="totalDisplay" class="font-bold text-2xl text-purple-600">{{ number_format($purchase->total, 2) }} ريال</span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('purchases.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="bg-coffee-600 hover:bg-coffee-700 text-white px-6 py-2 rounded-md">
                        تحديث الطلب
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let rowIndex = {{ count($purchase->items) }};

        function addPurchaseRow() {
            const tbody = document.getElementById('purchaseItems');
            const newRow = `
                <tr class="purchase-row">
                    <td class="px-4 py-3">
                        <select name="items[${rowIndex}][product_id]"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 product-select"
                            required onchange="updateCost(this)">
                            <option value="">-- اختر المنتج --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-cost="{{ $product->cost_price }}"
                                    data-stock="{{ $product->currentStock() }}">
                                    {{ $product->name_ar }} (مخزون حالي: {{ $product->currentStock() }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="items[${rowIndex}][quantity]" value="1" min="1" step="1"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 quantity-input"
                            required onchange="calculateRow(this)">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="items[${rowIndex}][cost_price]" value="0" min="0" step="0.01"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 cost-input"
                            required onchange="calculateRow(this)">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" value="0"
                            class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 total-input"
                            readonly>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="removeRow(this)"
                            class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', newRow);
            rowIndex++;
            calculateTotal();
        }

        function updateCost(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const cost = selectedOption.dataset.cost || 0;
            const row = selectElement.closest('tr');
            const costInput = row.querySelector('.cost-input');
            costInput.value = cost;
            calculateRow(costInput);
        }

        function calculateRow(inputElement) {
            const row = inputElement.closest('tr');
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            const total = quantity * cost;
            row.querySelector('.total-input').value = total.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            const totals = document.querySelectorAll('.total-input');
            let subtotal = 0;
            totals.forEach(input => {
                subtotal += parseFloat(input.value) || 0;
            });

            const tax = subtotal * 0.15;
            const total = subtotal + tax;

            document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ريال';
            document.getElementById('taxDisplay').textContent = tax.toFixed(2) + ' ريال';
            document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ريال';
        }

        function removeRow(button) {
            const tbody = document.getElementById('purchaseItems');
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 1) {
                button.closest('tr').remove();
                calculateTotal();
            } else {
                alert('يجب أن يحتوي الطلب على منتج واحد على الأقل');
            }
        }

        // Calculate initial totals on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
@endsection
