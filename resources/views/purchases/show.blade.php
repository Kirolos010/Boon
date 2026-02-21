@extends('layouts.app')

@section('title', 'عرض أمر شراء')
@section('navbar-title', 'عرض أمر شراء رقم ' . $purchase->purchase_number)

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- معلومات الطلب -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">أمر شراء</h2>
                    <p class="text-gray-600">رقم الطلب: <span class="font-semibold">{{ $purchase->purchase_number }}</span></p>
                    <p class="text-gray-600">تاريخ الطلب: <span class="font-semibold">{{ $purchase->purchase_date }}</span></p>
                    @if($purchase->expected_delivery_date)
                        <p class="text-gray-600">الاستلام المتوقع: <span class="font-semibold">{{ $purchase->expected_delivery_date }}</span></p>
                    @endif
                </div>
                <div class="text-left">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $purchase->status === 'received' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $purchase->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $purchase->status === 'pending' ? 'قيد الانتظار' : '' }}
                        {{ $purchase->status === 'received' ? 'تم الاستلام' : '' }}
                        {{ $purchase->status === 'cancelled' ? 'ملغي' : '' }}
                    </span>
                </div>
            </div>

            <!-- معلومات المورد -->
            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">المورد</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">الاسم: <span class="font-semibold">{{ $purchase->supplier->name_ar }}</span></p>
                        @if($purchase->supplier->email)
                            <p class="text-gray-600">البريد: <span class="font-semibold">{{ $purchase->supplier->email }}</span></p>
                        @endif
                    </div>
                    <div>
                        <p class="text-gray-600">الهاتف: <span class="font-semibold">{{ $purchase->supplier->phone }}</span></p>
                        <p class="text-gray-600">طريقة الدفع:
                            <span class="font-semibold">
                                {{ $purchase->payment_method === 'cash' ? 'نقدي' : '' }}
                                {{ $purchase->payment_method === 'credit' ? 'آجل' : '' }}
                                {{ $purchase->payment_method === 'bank_transfer' ? 'تحويل بنكي' : '' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- بنود الطلب -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">بنود الطلب</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">سعر الوحدة</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchase->items as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $item->product->name_ar }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($item->cost_price, 2) }} ريال</td>
                                <td class="px-4 py-3 text-gray-900 font-semibold">{{ number_format($item->quantity * $item->cost_price, 2) }} ريال</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- الملخص المالي -->
            <div class="flex justify-end">
                <div class="w-full md:w-1/2 lg:w-1/3">
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-700">المجموع الفرعي:</span>
                            <span class="font-semibold">{{ number_format($purchase->subtotal, 2) }} ريال</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-700">الضريبة (15%):</span>
                            <span class="font-semibold">{{ number_format($purchase->tax, 2) }} ريال</span>
                        </div>
                        <div class="flex justify-between items-center text-xl border-t pt-2 mt-2">
                            <span class="text-gray-900 font-bold">الإجمالي:</span>
                            <span class="font-bold text-purple-600">{{ number_format($purchase->total, 2) }} ريال</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            @if($purchase->notes)
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">ملاحظات</h3>
                <p class="text-gray-700">{{ $purchase->notes }}</p>
            </div>
            @endif

            <!-- تاريخ الاستلام -->
            @if($purchase->status === 'received' && $purchase->received_date)
            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-green-800 mb-2">✓ تم الاستلام</h3>
                <p class="text-green-700">التاريخ: {{ $purchase->received_date }}</p>
                @if($purchase->received_notes)
                    <p class="text-green-700 mt-2">ملاحظات الاستلام: {{ $purchase->received_notes }}</p>
                @endif
            </div>
            @endif
        </div>

        <!-- أزرار الإجراءات -->
        <div class="flex justify-between items-center">
            <a href="{{ route('purchases.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                العودة
            </a>
            <div class="flex gap-2">
                @if($purchase->status === 'pending')
                    <button onclick="openReceiveModal()"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                        تأكيد الاستلام
                    </button>
                    @can('edit', $purchase)
                        <a href="{{ route('purchases.edit', $purchase) }}"
                            class="bg-coffee-600 hover:bg-coffee-700 text-white px-6 py-2 rounded-md">
                            تعديل
                        </a>
                    @endcan
                @endif
                <button onclick="window.print()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    طباعة
                </button>
            </div>
        </div>
    </div>

    <!-- Receive Modal -->
    <div id="receiveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">تأكيد استلام الطلبية</h3>
                <form action="{{ route('purchases.receive', $purchase) }}" method="POST">
                    @csrf

                    <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>ملاحظة:</strong> سيتم إضافة الكميات التالية للمخزون:
                        </p>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            @foreach($purchase->items as $item)
                                <li>• {{ $item->product->name_ar }}: {{ $item->quantity }} وحدة</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">تاريخ الاستلام *</label>
                        <input type="date" name="received_date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">ملاحظات الاستلام</label>
                        <textarea name="received_notes" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500"
                            placeholder="أي ملاحظات على حالة البضاعة المستلمة..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeReceiveModal()"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                            إلغاء
                        </button>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            تأكيد الاستلام
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openReceiveModal() {
            document.getElementById('receiveModal').classList.remove('hidden');
        }

        function closeReceiveModal() {
            document.getElementById('receiveModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('receiveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReceiveModal();
            }
        });
    </script>

    <style>
        @media print {
            .no-print, button, a {
                display: none !important;
            }
            body {
                background: white;
            }
        }
    </style>
@endsection
