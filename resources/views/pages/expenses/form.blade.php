@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <form id="expenseForm" action="{{ route('expenses.store') }}" method="POST">
        @csrf

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">บันทึกรายจ่ายใหม่</h1>
                <p class="text-sm text-gray-500 font-kanit">บันทึกข้อมูลการจ่ายเงินและจัดหมวดหมู่บัญชี</p>
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
                        <h3 class="text-sm font-bold text-red-800 font-kanit">เกิดข้อผิดพลาดในการบันทึก:</h3>
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
                    <i class="fas fa-file-invoice-dollar mr-2 text-blue-500"></i>
                    รายละเอียดรายการจ่ายเงิน
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
                                   value="{{ old('expense_date', $expense->expense_date ?? date('Y-m-d')) }}"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('expense_date') border-red-500 @enderror">
                        </div>
                    </div>

                    {{-- ผู้จำหน่าย/ผู้รับเงิน --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผู้จำหน่าย / ผู้รับเงิน</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-store-alt text-xs"></i>
                            </div>
                            <select name="payee_id" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                                <option value="">-- เลือกผู้จำหน่าย --</option>
                                @foreach($payees as $payee)
                                    <option value="{{ $payee->id }}" @selected(old('payee_id', $expense->payee_id ?? '') == $payee->id)>
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
                            <textarea name="description" rows="3"
                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror"
                                      placeholder="ระบุรายละเอียดของค่าใช้จ่าย เช่น ค่าอุปกรณ์สำนักงาน, ค่าซ่อมบำรุง...">{{ old('description', $expense->description ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- จำนวนเงิน --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">จำนวนเงิน (บาท) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold text-xs">
                                ฿
                            </div>
                            <input type="number" step="0.01" name="amount"
                                   value="{{ old('amount', $expense->amount ?? 0) }}"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-blue-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-right"
                                   placeholder="0.00">
                        </div>
                    </div>

                    {{-- ผังบัญชี --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผังบัญชี / หมวดหมู่</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-book text-xs"></i>
                            </div>
                            <select name="account_id" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                                <option value="">-- เลือกผังบัญชี --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}")>
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
                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                      placeholder="หมายเหตุภายใน หรือข้อมูลอ้างอิงเลขที่เอกสาร...">{{ old('remark', $expense->remark ?? '') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer info --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <p class="text-[10px] text-gray-400 font-kanit italic">
                    * กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนกดบันทึก ระบบจะลงบัญชีแยกประเภทให้อัตโนมัติตามผังบัญชีที่เลือก
                </p>
            </div>
        </div>
    </form>
</div>
@endsection
