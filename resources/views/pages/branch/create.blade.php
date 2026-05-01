@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 md:py-12 max-w-4xl">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-10 gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 font-kanit tracking-tight">เพิ่มสาขาใหม่</h1>
            <p class="text-xs md:text-sm text-gray-500 font-kanit">สร้างข้อมูลสาขาเพื่อกระจายการบริหารจัดการในระบบ</p>
        </div>

        <div class="flex items-center space-x-3 w-full md:w-auto">
            <a href="{{ route('branches.index') }}"
               class="flex-1 md:flex-none text-center px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all font-kanit">
                ยกเลิก
            </a>

            <button type="submit" form="branchForm"
                    class="flex-1 md:flex-none px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-100 font-kanit flex items-center justify-center">
                <i class="fas fa-save mr-2 text-xs"></i> บันทึกข้อมูล
            </button>
        </div>
    </div>

    {{-- FORM CARD --}}
    <form id="branchForm" action="{{ route('branches.store') }}" method="POST"
          class="bg-white rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        @csrf

        <div class="p-6 md:p-12 space-y-6 md:space-y-10">

            {{-- Section 1: Basic Information --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 md:gap-8">
                <div class="md:col-span-4 space-y-2">
                    <label class="text-[10px] md:text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">รหัสสาขา (Auto)</label>
                    <input type="text" name="code" value="{{ $autoCode ?? 'BR-0001' }}" readonly
                           class="block w-full px-4 py-3 border border-gray-100 rounded-xl bg-gray-50/80 text-gray-500 font-mono text-sm cursor-not-allowed">
                </div>

                <div class="md:col-span-8 space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">
                        ชื่อสาขา <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-store text-xs"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="สาขาพระราม 9 / สาขาสำนักงานใหญ่"
                               class="block w-full pl-11 pr-4 py-3 border @error('name') border-red-300 @else border-gray-200 @enderror rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-kanit">
                    </div>
                    @error('name') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-12 space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">ผู้จัดการสาขา</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-user-tie text-xs"></i>
                        </div>
                        <input type="text" name="manager" value="{{ old('manager') }}" placeholder="ชื่อ-นามสกุล ผู้รับผิดชอบ"
                               class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-kanit">
                    </div>
                </div>
            </div>

            <hr class="border-gray-50">

            {{-- Section 2: Contact --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8">
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">อีเมลสาขา</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="branch@company.com"
                           class="block w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">เบอร์โทรศัพท์สาขา</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08X-XXX-XXXX"
                           class="block w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 font-kanit">ที่ตั้งสาขา</label>
                    <textarea name="address" rows="3" placeholder="ระบุที่อยู่ของสาขาโดยละเอียด..."
                              class="block w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all resize-none font-kanit">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="bg-gray-50/50 px-6 py-6 md:px-12 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center text-gray-400">
                <i class="fas fa-info-circle text-blue-500 mr-2 text-sm"></i>
                <span class="text-[11px] md:text-xs font-medium font-kanit leading-tight">สาขาที่เพิ่มใหม่จะถูกตั้งค่าเป็น "เปิดใช้งาน" โดยอัตโนมัติ</span>
            </div>

            <label class="relative inline-flex items-center cursor-pointer group">
                <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                <span class="ml-3 text-xs font-bold text-gray-600 font-kanit group-hover:text-green-600 transition-colors">สถานะ: เปิดใช้งาน</span>
            </label>
        </div>
    </form>
</div>
@endsection
