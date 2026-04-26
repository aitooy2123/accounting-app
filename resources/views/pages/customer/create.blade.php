@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">เพิ่มลูกค้าใหม่</h1>
    <form action="{{ route('customers.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700">รหัสลูกค้า <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded px-3 py-2" required>
                @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700">ชื่อลูกค้า/บริษัท <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700">อีเมล</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700">เบอร์โทรศัพท์</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700">ที่อยู่</label>
                <textarea name="address" class="w-full border rounded px-3 py-2" rows="2">{{ old('address') }}</textarea>
            </div>
            <div>
                <label class="block text-gray-700">เลขผู้เสียภาษี</label>
                <input type="text" name="tax_id" value="{{ old('tax_id') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700">ผู้ติดต่อ</label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700">เบอร์โทรผู้ติดต่อ</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full border rounded px-3 py-2">
            </div>

            {{-- Branch and Company dropdowns --}}
            <div>
                <label class="block text-gray-700">สังกัดสาขา</label>
                <select name="branch_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- ไม่ระบุสาขา --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700">บริษัท</label>
                <select name="company_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- ไม่ระบุบริษัท --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="inline-flex items-center mt-4">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                    <span class="text-gray-700">เปิดใช้งาน</span>
                </label>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <a href="{{ route('customers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">ยกเลิก</a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">บันทึก</button>
        </div>
    </form>
</div>
@endsection
