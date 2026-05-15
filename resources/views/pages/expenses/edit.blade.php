@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- เปลี่ยน Route เป็น update และเพิ่ม @method('PUT') --}}
    <form id="expenseForm" action="{{ route('expenses.update', $expense->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">แก้ไขรายการจ่ายเงิน</h1>
                <p class="text-sm text-gray-500 font-kanit">เลขที่เอกสาร: <span class="text-blue-600 font-bold">{{ $expense->doc_no }}</span></p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('expenses.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all font-kanit">ยกเลิก</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200 font-kanit">
                    <i class="fas fa-check-circle mr-2"></i> อัปเดตรายการ
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
                    <i class="fas fa-edit mr-2 text-blue-500"></i>
                    แก้ไขรายละเอียด
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
value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d')) }}"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    {{-- ผู้จำหน่าย/ผู้รับเงิน --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผู้จำหน่าย / ผู้รับเงิน</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-store-alt text-xs"></i>
                            </div>
                            <select name="payee_id" class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                                <option value="">-- เลือกผู้จำหน่าย --</option>
                                @foreach($payees as $payee)
                                    <option value="{{ $payee->id }}" @selected(old('payee_id', $expense->payee_id) == $payee->id)>
                                        {{ $payee->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    {{-- รายละเอียด --}}
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">รายละเอียดรายการ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-start pointer-events-none text-gray-400 pt-3">
                                <i class="fas fa-list text-xs"></i>
                            </div>
                            <textarea name="description" rows="2"
                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ old('description', $expense->description) }}</textarea>
                        </div>
                    </div>

                    {{-- จำนวนเงิน --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">จำนวนเงิน (ก่อนภาษี) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold text-xs">
                                ฿
                            </div>
                            <input type="number" step="0.01" name="amount" id="amount"
                                   value="{{ old('amount', $expense->amount) }}"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-blue-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right">
                        </div>
                    </div>

                    {{-- ภาษีมูลค่าเพิ่ม (VAT) --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ภาษีมูลค่าเพิ่ม (VAT)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-percent text-xs"></i>
                            </div>
                            <select name="vat_rate" id="vat_rate" class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                                <option value="0" @selected(old('vat_rate', $expense->vat_rate) == 0)>0% (ไม่มี VAT)</option>
                                <option value="7" @selected(old('vat_rate', $expense->vat_rate) == 7)>7% (มาตรฐาน)</option>
                                <option value="10" @selected(old('vat_rate', $expense->vat_rate) == 10)>10%</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    {{-- สถานะการชำระเงิน --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">สถานะการชำระเงิน <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach([
                                'paid' => ['label' => 'ชำระแล้ว', 'color' => 'peer-checked:bg-green-50 peer-checked:text-green-600 peer-checked:border-green-500'],
                                'pending' => ['label' => 'ค้างชำระ', 'color' => 'peer-checked:bg-amber-50 peer-checked:text-amber-600 peer-checked:border-amber-500'],
                                'invoiced' => ['label' => 'ออกใบแจ้งหนี้', 'color' => 'peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-500']
                            ] as $key => $style)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="{{ $key }}" class="hidden peer" {{ old('status', $expense->status) == $key ? 'checked' : '' }}>
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
                                    <option value="{{ $acc->id }}" @selected(old('account_id', $expense->account_id) == $acc->id)>
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
                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ old('remark', $expense->remark) }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer info --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <p class="text-[10px] text-gray-400 font-kanit italic">
                    * แก้ไขข้อมูลแล้วอย่าลืมตรวจสอบความถูกต้องของยอดรวมก่อนบันทึก
                </p>
                <div id="total_display" class="text-right">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">ยอดรวมสุทธิ (Grand Total)</span>
                    <span id="grand_total" class="text-lg font-bold text-gray-900">฿ 0.00</span>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Script คำนวณยอดรวม (เหมือนหน้า Create) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const vatSelect = document.getElementById('vat_rate');
        const grandTotalDisplay = document.getElementById('grand_total');

        function calculateTotal() {
            const amount = parseFloat(amountInput.value) || 0;
            const vatRate = parseFloat(vatSelect.value) || 0;
            const vatAmount = (amount * vatRate) / 100;
            const total = amount + vatAmount;
            grandTotalDisplay.innerText = '฿ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        amountInput.addEventListener('input', calculateTotal);
        vatSelect.addEventListener('change', calculateTotal);

        calculateTotal();
    });
</script>
@endsection
