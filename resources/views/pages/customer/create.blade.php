@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">เพิ่มลูกค้าใหม่</h1>
            <p class="text-sm text-gray-500 font-kanit">เพิ่มข้อมูลลูกค้าและติดตามรายการขาย</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">ยกเลิก</a>
            <button type="submit" form="customerForm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200 font-kanit">
                <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-sm font-bold text-red-800">เกิดข้อผิดพลาดในการบันทึก:</h3>
                    <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="customerForm" action="{{ route('customers.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @csrf

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- รหัสลูกค้า (Auto Generate) --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">รหัสลูกค้า <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-id-card text-xs"></i>
                        </div>
                        <input type="text" name="code" id="customer_code" value="{{ old('code', $autoCode ?? '') }}"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-600"
                               readonly>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">ระบบสร้างรหัสอัตโนมัติ</p>
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ชื่อลูกค้า/บริษัท --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ชื่อลูกค้า/บริษัท <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-building text-xs"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                               placeholder="เช่น บริษัท เอ บี ซี จำกัด" required>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- อีเมล --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">อีเมล</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-envelope text-xs"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                               placeholder="example@company.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- เบอร์โทรศัพท์ --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เบอร์โทรศัพท์</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-phone text-xs"></i>
                        </div>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('phone') border-red-500 @enderror"
                               placeholder="0xx-xxx-xxxx">
                    </div>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ที่อยู่ --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ที่อยู่</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-start pointer-events-none text-gray-400 pt-3">
                            <i class="fas fa-map-marker-alt text-xs"></i>
                        </div>
                        <textarea name="address" rows="3"
                                  class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('address') border-red-500 @enderror"
                                  placeholder="เลขที่ หมู่ที่ ซอย ถนน ตำบล/แขวง อำเภอ/เขต จังหวัด รหัสไปรษณีย์">{{ old('address') }}</textarea>
                    </div>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- เลขผู้เสียภาษี --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลขประจำตัวผู้เสียภาษี</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-file-invoice text-xs"></i>
                        </div>
                        <input type="text" name="tax_id" value="{{ old('tax_id') }}"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('tax_id') border-red-500 @enderror"
                               placeholder="xxxxxxxxxxxxx">
                    </div>
                    @error('tax_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ผู้ติดต่อ --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผู้ติดต่อ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('contact_person') border-red-500 @enderror"
                               placeholder="ชื่อผู้ประสานงาน">
                    </div>
                    @error('contact_person')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- เบอร์โทรผู้ติดต่อ --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เบอร์โทรผู้ติดต่อ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-phone-alt text-xs"></i>
                        </div>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('contact_phone') border-red-500 @enderror"
                               placeholder="เบอร์โทรผู้ประสานงาน">
                    </div>
                    @error('contact_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- สังกัดสาขา --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">สังกัดสาขา</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-store text-xs"></i>
                        </div>
                        <select name="branch_id" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">-- ไม่ระบุสาขา --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('branch_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- บริษัท --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">บริษัท</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-building text-xs"></i>
                        </div>
                        <select name="company_id" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">-- ไม่ระบุบริษัท --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('company_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- สถานะการใช้งาน --}}
                <div class="md:col-span-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">เปิดใช้งานลูกค้า</span>
                    </label>
                    @error('is_active')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </form>
</div>


@endsection
