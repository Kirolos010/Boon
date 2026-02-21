@extends('layouts.app')

@section('title', 'مبيعات سريعة')
@section('navbar-title', 'مبيعات سريعة - إضافة جديد')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('quick-sales.store') }}" method="POST" id="quickSaleForm">
                @csrf

                <!-- معلومات البيع -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- تاريخ البيع -->
                    <x-form-group
                        name="sale_date"
                        label="تاريخ البيع"
                        :required="true"
                    >
                        <input type="date" name="sale_date" id="sale_date"
                            value="{{ old('sale_date', date('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                    </x-form-group>

                    <!-- اسم العميل (اختياري) -->
                    <x-form-group
                        name="customer_name"
                        label="اسم العميل (اختياري)"
                    >
                        <input type="text" name="customer_name" id="customer_name"
                            value="{{ old('customer_name') }}"
                            placeholder="اسم العميل إن وجد"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500">
                    </x-form-group>
                </div>

                <!-- المنتجات -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">المنتجات</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">السعر</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجمالي</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="saleItems" class="bg-white divide-y divide-gray-200">
                                <tr class="sale-row">
                                    <td class="px-4 py-3">
                                        <select name="items[0][product_id]"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 product-select"
                                            required onchange="updatePrice(this)">
                                            <option value="">-- اختر المنتج --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->selling_price }}"
                                                    data-stock="{{ $product->currentStock() }}">
                                                    {{ $product->name_ar }} (متوفر: {{ $product->currentStock() }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[0][quantity]" value="1" min="1" step="1"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 quantity-input"
                                            required onchange="calculateRow(this)">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[0][price]" value="0" min="0" step="0.01"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 price-input"
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
                            </tbody>
                        </table>
                    </div>

                    <button type="button" onclick="addSaleRow()"
                        class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                        + إضافة منتج
                    </button>
                </div>

                <!-- طريقة الدفع -->
                <div class="mb-6">
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
                </div>

                <!-- الملاحظات -->
                <div class="mb-6">
                    <x-form-group name="notes" label="ملاحظات (اختياري)">
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500">{{ old('notes') }}</textarea>
                    </x-form-group>
                </div>

                <!-- الملخص المالي -->
                <div class="bg-gradient-to-br from-coffee-50 to-coffee-100 rounded-lg p-6 mb-6 border border-coffee-200">
                    <h3 class="text-lg font-semibold text-coffee-900 mb-4">ملخص البيع</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2">
                            <span class="text-gray-700 font-medium">المجموع الفرعي:</span>
                            <span id="subtotalDisplay" class="font-bold text-lg text-gray-900">0.00 ريال</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-coffee-300">
                            <span class="text-gray-700 font-medium">الضريبة (15%):</span>
                            <span id="taxDisplay" class="font-bold text-lg text-gray-900">0.00 ريال</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-gray-900 font-bold text-xl">الإجمالي النهائي:</span>
                            <span id="totalDisplay" class="font-bold text-2xl text-coffee-600">0.00 ريال</span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('quick-sales.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="bg-coffee-600 hover:bg-coffee-700 text-white px-6 py-2 rounded-md">
                        حفظ البيع
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let rowIndex = 1;

        function addSaleRow() {
            const tbody = document.getElementById('saleItems');
            const newRow = `
                <tr class="sale-row">
                    <td class="px-4 py-3">
                        <select name="items[${rowIndex}][product_id]"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 product-select"
                            required onchange="updatePrice(this)">
                            <option value="">-- اختر المنتج --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-price="{{ $product->selling_price }}"
                                    data-stock="{{ $product->currentStock() }}">
                                    {{ $product->name_ar }} (متوفر: {{ $product->currentStock() }})
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
                        <input type="number" name="items[${rowIndex}][price]" value="0" min="0" step="0.01"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 price-input"
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
        }

        function updatePrice(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const price = selectedOption.dataset.price || 0;
            const row = selectElement.closest('tr');
            const priceInput = row.querySelector('.price-input');
            priceInput.value = price;
            calculateRow(priceInput);
        }

        function calculateRow(inputElement) {
            const row = inputElement.closest('tr');
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = quantity * price;
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
            const tbody = document.getElementById('saleItems');
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 1) {
                button.closest('tr').remove();
                calculateTotal();
            } else {
                alert('يجب أن يحتوي البيع على منتج واحد على الأقل');
            }
        }

        // Validate stock before submission
        document.getElementById('quickSaleForm').addEventListener('submit', function(e) {
            let valid = true;
            const rows = document.querySelectorAll('.sale-row');

            rows.forEach(row => {
                const select = row.querySelector('.product-select');
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const selectedOption = select.options[select.selectedIndex];
                const stock = parseFloat(selectedOption.dataset.stock) || 0;

                if (quantity > stock) {
                    valid = false;
                    alert(`الكمية المطلوبة (${quantity}) أكبر من المخزون المتاح (${stock}) للمنتج: ${selectedOption.text}`);
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
@endsection
