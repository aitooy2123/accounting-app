@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">เพิ่มสาขาใหม่</h1>
    <form action="{{ route('branches.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">รหัสสาขา <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded px-3 py-2" required>
            @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">ชื่อสาขา <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">ที่อยู่</label>
            <textarea name="address" class="w-full border rounded px-3 py-2" rows="2">{{ old('address') }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">เบอร์โทรศัพท์</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">อีเมล</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">ผู้จัดการสาขา</label>
            <input type="text" name="manager" value="{{ old('manager') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="mr-2">
                <span class="text-gray-700">เปิดใช้งานสาขานี้</span>
            </label>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('branches.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">ยกเลิก</a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">บันทึก</button>
        </div>
    </form>
</div>
@endsection
