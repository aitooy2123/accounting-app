@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 md:py-12 max-w-4xl">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-10 gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 font-kanit tracking-tight">เพิ่มรหัสบัญชีใหม่</h1>
            <p class="text-xs md:text-sm text-gray-500 font-kanit">กำหนดโครงสร้างผังบัญชีเพื่อใช้ในการบันทึกข้อมูลทางการเงิน</p>
        </div>

        <div class="flex items-center space-x-3 w-full md:w-auto">
            <a href="{{ route('accounts.index') }}"
               class="flex-1 md:flex-none text-center px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all font-kanit">
                ยกเลิก
            </a>

            <button type="submit" form="accountForm"
                    class="flex-1 md:flex-none px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-emerald-100 font-kanit flex items-center justify-center">
                <i class="fas fa-save mr-2 text-xs"></i> บันทึกบัญชี
            </button>
        </div>
    </div>

    {{-- FORM CARD --}}
    <form id="accountForm" action="{{ route('accounts.store') }}" method="POST"
          class="bg-white rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        @csrf

        <div class="p-6 md:p-12 space-y-6 md:space-y-10">

            {{-- Section 1: Account Identity --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 md:gap-8">
                <div class="md:col-span-4 space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">
                        รหัสบัญชี <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="เช่น 1111-00"
                           class="block w-full px-4 py-3 border @error('code') border-red-300 @else border-gray-200 @enderror rounded-xl text-sm font-mono focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                    @error('code') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-8 space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">
                        หมวดหมู่บัญชี <span class="text-red-500">*</span>
                    </label>
                    <select name="category" required
                            class="block w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-kanit">
                        <option value="">เลือกหมวดหมู่...</option>
                        <option value="asset" {{ old('category') == 'asset' ? 'selected' : '' }}>1. สินทรัพย์ (Asset)</option>
                        <option value="liability" {{ old('category') == 'liability' ? 'selected' : '' }}>2. หนี้สิน (Liability)</option>
                        <option value="equity" {{ old('category') == 'equity' ? 'selected' : '' }}>3. ส่วนของเจ้าของ (Equity)</option>
                        <option value="revenue" {{ old('category') == 'revenue' ? 'selected' : '' }}>4. รายได้ (Revenue)</option>
                        <option value="expense" {{ old('category') == 'expense' ? 'selected' : '' }}>5. ค่าใช้จ่าย (Expense)</option>
                    </select>
                </div>

                <div class="md:col-span-12 space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">
                        ชื่อบัญชี (ภาษาไทย) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                            <i class="fas fa-file-invoice text-xs"></i>
                        </div>
                        <input type="text" name="name_th" value="{{ old('name_th') }}" required placeholder="เช่น เงินสดในมือ, รายได้จากการขาย"
                               class="block w-full pl-11 pr-4 py-3 border @error('name_th') border-red-300 @else border-gray-200 @enderror rounded-xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-kanit">
                    </div>
                </div>

                <div class="md:col-span-12 space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">ชื่อบัญชี (ภาษาอังกฤษ)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" placeholder="e.g. Cash on hand, Sale Revenue"
                           class="block w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                </div>
            </div>

            <hr class="border-gray-50">

            {{-- Section 2: Hierarchy & Logic --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8">
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">อยู่ภายใต้บัญชี (Parent)</label>
                    <select name="parent_id"
                            class="block w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-kanit">
                        <option value="">เป็นบัญชีหลัก (Root)</option>
                        @foreach($parentAccounts as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->code }} - {{ $parent->name_th }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">ประเภทการบันทึก</label>
                    <div class="flex items-center space-x-4 h-[50px]">
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="is_group" value="1" class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500" {{ old('is_group') == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-kanit text-gray-600 group-hover:text-emerald-600 transition-colors">บัญชีคุม (บันทึกรายการไม่ได้)</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="is_group" value="0" class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500" {{ old('is_group', '0') == '0' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-kanit text-gray-600 group-hover:text-emerald-600 transition-colors">บัญชีย่อย</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="bg-gray-50/50 px-6 py-6 md:px-12 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center text-gray-400">
                <i class="fas fa-shield-alt text-emerald-500 mr-2 text-sm"></i>
                <span class="text-[11px] md:text-xs font-medium font-kanit leading-tight">การบันทึกผังบัญชีที่ถูกต้องจะช่วยให้งบการเงินมีความแม่นยำ</span>
            </div>

            <label class="relative inline-flex items-center cursor-pointer group">
                <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                <span class="ml-3 text-xs font-bold text-gray-600 font-kanit group-hover:text-emerald-600 transition-colors">เปิดใช้งานรหัสบัญชีนี้</span>
            </label>
        </div>
    </form>
</div>
@endsection
