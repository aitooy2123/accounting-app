@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <form id="expenseForm" action="{{ route('expenses.store') }}" method="POST">
        @csrf

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">สร้างรายการจ่ายเงินใหม่</h1>
                <p class="text-sm text-gray-500 font-kanit">บันทึกรายละเอียดค่าใช้จ่ายและรายการย่อย</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('expenses.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all font-kanit">ยกเลิก</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200 font-kanit">
                    <i class="fas fa-save mr-2"></i> บันทึกรายการ
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800 font-kanit">พบข้อผิดพลาด:</h3>
                        <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Expense Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-700 font-kanit flex items-center">
                    <i class="fas fa-plus-circle mr-2 text-blue-500"></i>
                    กรอกรายละเอียดข้อมูล
                </h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- วันที่จ่าย --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">วันที่รายการ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-alt text-xs"></i>
                            </div>
                            <input type="date" name="expense_date"
                                   value="{{ old('expense_date', date('Y-m-d')) }}"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    {{-- เลขที่เอกสาร --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลขที่เอกสาร / อ้างอิง</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-file-invoice text-xs"></i>
                            </div>
                            <input type="text" name="doc_no"
                                   value="{{ old('doc_no') }}"
                                   placeholder="เช่น INV6705001"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    {{-- ผู้จำหน่าย --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผู้จำหน่าย / ผู้รับเงิน</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-store-alt text-xs"></i>
                            </div>
                            <select name="payee_id" class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                                <option value="">-- เลือกผู้จำหน่าย --</option>
                                @foreach($payees as $payee)
                                    <option value="{{ $payee->id }}" @selected(old('payee_id') == $payee->id)>
                                        {{ $payee->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    รายละเอียดหลัก
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">รายละเอียดรายการ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-start pointer-events-none text-gray-400 pt-3">
                                <i class="fas fa-list text-xs"></i>
                            </div>
                            <textarea name="description" rows="2" placeholder="ระบุเหตุผลหรือรายละเอียดการจ่าย"
                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- จำนวนเงิน (ถ้าไม่มีรายการย่อย) --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">จำนวนเงิน (ก่อนภาษี) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold text-xs">
                                ฿
                            </div>
                            <input type="number" step="0.01" name="amount" id="amount"
                                   value="{{ old('amount') }}"
                                   placeholder="0.00"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-blue-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">ใช้กรณีไม่มีรายการย่อย (items)</p>
                    </div>

                    {{-- ภาษี --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ภาษีมูลค่าเพิ่ม (VAT)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-percent text-xs"></i>
                            </div>
                            <select name="vat_rate" id="vat_rate" class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                                <option value="0" @selected(old('vat_rate') == 0)>0% (ไม่มี VAT)</option>
                                <option value="7" @selected(old('vat_rate', 7) == 7)>7% (มาตรฐาน)</option>
                                <option value="10" @selected(old('vat_rate') == 10)>10%</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    {{-- สถานะ --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">สถานะการชำระเงิน <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            @php $status = old('status', 'paid'); @endphp
                            @foreach([
                                'paid' => ['label' => 'ชำระแล้ว', 'color' => 'peer-checked:bg-green-50 peer-checked:text-green-600 peer-checked:border-green-500'],
                                'pending' => ['label' => 'ค้างชำระ', 'color' => 'peer-checked:bg-amber-50 peer-checked:text-amber-600 peer-checked:border-amber-500'],
                                'invoiced' => ['label' => 'ออกใบแจ้งหนี้', 'color' => 'peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-500']
                            ] as $key => $style)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="{{ $key }}" class="hidden peer" {{ $status == $key ? 'checked' : '' }}>
                                    <div class="text-center py-2.5 text-[12px] font-bold rounded-xl border border-gray-200 transition-all font-kanit {{ $style['color'] }} bg-white text-gray-500">
                                        {{ $style['label'] }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ผังบัญชี --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผังบัญชี / หมวดหมู่</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-book text-xs"></i>
                            </div>
                            <select name="account_id" class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                                <option value="">-- เลือกผังบัญชี --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" @selected(old('account_id') == $acc->id)>
                                        {{ $acc->code }} - {{ $acc->name_th }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    {{-- หมายเหตุ --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">หมายเหตุเพิ่มเติม</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-start pointer-events-none text-gray-400 pt-3">
                                <i class="fas fa-sticky-note text-xs"></i>
                            </div>
                            <textarea name="remark" rows="2"
                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ old('remark') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Items Section (Dynamic) --}}
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-700 font-kanit flex items-center">
                    <i class="fas fa-list-ul mr-2 text-blue-500"></i>
                    รายการค่าใช้จ่ายย่อย (Items)
                </h3>
                <button type="button" id="addItemBtn" class="text-xs bg-white border border-gray-200 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-all">
                    <i class="fas fa-plus mr-1"></i> เพิ่มรายการ
                </button>
            </div>

            <div class="p-6">
                <table class="w-full" id="itemsTable">
                    <thead>
                        <tr class="text-left text-[11px] font-bold text-gray-400 uppercase border-b border-gray-100">
                            <th class="pb-3 w-2/5">รายละเอียด</th>
                            <th class="pb-3 w-1/6 text-right">จำนวน</th>
                            <th class="pb-3 w-1/6 text-right">ราคาต่อหน่วย</th>
                            <th class="pb-3 w-1/6 text-right">รวม</th>
                            <th class="pb-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        {{-- กรณี validation error คืนค่า items เก่า --}}
                        @php $oldItems = old('items', []); @endphp
                        @forelse($oldItems as $index => $item)
                            <tr class="item-row border-b border-gray-50">
                                <td class="py-3 pr-2">
                                    <input type="text" name="items[{{ $index }}][desc]" value="{{ $item['desc'] ?? '' }}" placeholder="รายการ..." class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:ring-blue-500">
                                </td>
                                <td class="py-3 pr-2">
                                    <input type="number" step="any" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? 1 }}" class="item-qty w-full text-right border border-gray-200 rounded-lg text-sm px-3 py-2 focus:ring-blue-500">
                                </td>
                                <td class="py-3 pr-2">
                                    <input type="number" step="any" name="items[{{ $index }}][price]" value="{{ $item['price'] ?? 0 }}" class="item-price w-full text-right border border-gray-200 rounded-lg text-sm px-3 py-2 focus:ring-blue-500">
                                </td>
                                <td class="py-3 pr-2">
                                    <span class="item-total block text-right text-sm font-medium text-gray-700">0.00</span>
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button" class="remove-item text-red-400 hover:text-red-600 transition-all"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr class="item-row" id="emptyRowPlaceholder">
                                <td colspan="5" class="py-6 text-center text-gray-400 text-sm">ยังไม่มีรายการย่อย คลิก "เพิ่มรายการ" ข้างบน</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer info และรวมยอด --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <p class="text-[10px] text-gray-400 font-kanit italic">
                    * รายการย่อยจะถูกนำมาคำนวณรวมแทนฟิลด์ "จำนวนเงิน" (ถ้ามี)
                </p>
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">ยอดรวมสุทธิ (Grand Total)</span>
                    <span id="grand_total" class="text-lg font-bold text-gray-900">฿ 0.00</span>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ตัวแปรสำหรับลำดับ items (ใช้ next index)
        let itemIndex = {{ count(old('items', [])) }};

        function calculateAll() {
            let subtotal = 0;
            const rows = document.querySelectorAll('#itemsBody .item-row');
            rows.forEach(row => {
                if (row.id === 'emptyRowPlaceholder') return;
                const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
                const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
                const total = qty * price;
                const totalSpan = row.querySelector('.item-total');
                if (totalSpan) totalSpan.innerText = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                subtotal += total;
            });

            const amountInput = document.getElementById('amount');
            const vatSelect = document.getElementById('vat_rate');
            let beforeVat = subtotal;
            if (beforeVat === 0 && amountInput) {
                beforeVat = parseFloat(amountInput.value) || 0;
            }
            const vatRate = parseFloat(vatSelect.value) || 0;
            const vatAmount = (beforeVat * vatRate) / 100;
            const grandTotal = beforeVat + vatAmount;
            document.getElementById('grand_total').innerText = '฿ ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // ถ้ามีรายการย่อย ให้ disable amount field
            const hasItems = rows.length > 0 && !document.getElementById('emptyRowPlaceholder');
            if (amountInput) {
                if (hasItems) {
                    amountInput.disabled = true;
                    amountInput.classList.add('bg-gray-100');
                } else {
                    amountInput.disabled = false;
                    amountInput.classList.remove('bg-gray-100');
                }
            }
        }

        function attachRowEvents(row) {
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');
            const removeBtn = row.querySelector('.remove-item');
            if (qtyInput) qtyInput.addEventListener('input', calculateAll);
            if (priceInput) priceInput.addEventListener('input', calculateAll);
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    row.remove();
                    // ถ้าไม่มีแถวใดเหลือเลย ให้สร้าง placeholder
                    if (document.querySelectorAll('#itemsBody .item-row:not(#emptyRowPlaceholder)').length === 0) {
                        const tbody = document.getElementById('itemsBody');
                        // ถ้ายังไม่มี placeholder ให้เพิ่ม
                        if (!document.getElementById('emptyRowPlaceholder')) {
                            const placeholderRow = document.createElement('tr');
                            placeholderRow.id = 'emptyRowPlaceholder';
                            placeholderRow.className = 'item-row';
                            placeholderRow.innerHTML = '<td colspan="5" class="py-6 text-center text-gray-400 text-sm">ยังไม่มีรายการย่อย คลิก "เพิ่มรายการ" ข้างบน</td>';
                            tbody.appendChild(placeholderRow);
                        }
                    }
                    calculateAll();
                });
            }
        }

        function addItemRow() {
            const tbody = document.getElementById('itemsBody');
            // ลบ placeholder ถ้ามี
            const placeholder = document.getElementById('emptyRowPlaceholder');
            if (placeholder) placeholder.remove();

            const newRow = document.createElement('tr');
            newRow.className = 'item-row border-b border-gray-50';
            newRow.innerHTML = `
                <td class="py-3 pr-2"><input type="text" name="items[${itemIndex}][desc]" placeholder="รายการ..." class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2"></td>
                <td class="py-3 pr-2"><input type="number" step="any" name="items[${itemIndex}][qty]" value="1" class="item-qty w-full text-right border border-gray-200 rounded-lg text-sm px-3 py-2"></td>
                <td class="py-3 pr-2"><input type="number" step="any" name="items[${itemIndex}][price]" value="0" class="item-price w-full text-right border border-gray-200 rounded-lg text-sm px-3 py-2"></td>
                <td class="py-3 pr-2"><span class="item-total block text-right text-sm font-medium text-gray-700">0.00</span></td>
                <td class="py-3 text-center"><button type="button" class="remove-item text-red-400 hover:text-red-600"><i class="fas fa-trash-alt"></i></button></td>
            `;
            tbody.appendChild(newRow);
            attachRowEvents(newRow);
            itemIndex++;
            calculateAll();
        }

        // ผูก events กับแถวที่มีอยู่แล้ว (รวม placeholder? ไม่จำเป็น)
        document.querySelectorAll('#itemsBody .item-row').forEach(row => {
            if (row.id !== 'emptyRowPlaceholder') attachRowEvents(row);
        });

        // events สำหรับ amount และ vat
        const amountInput = document.getElementById('amount');
        const vatSelect = document.getElementById('vat_rate');
        if (amountInput) amountInput.addEventListener('input', calculateAll);
        if (vatSelect) vatSelect.addEventListener('change', calculateAll);

        // ปุ่มเพิ่มรายการ
        const addBtn = document.getElementById('addItemBtn');
        if (addBtn) addBtn.addEventListener('click', addItemRow);

        // คำนวณครั้งแรก
        calculateAll();
    });
</script>
@endsection
