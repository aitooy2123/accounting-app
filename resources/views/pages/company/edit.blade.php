@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Edit Company</h1>

    <form action="{{ route('companies.update', $company) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')  {{-- Important for update --}}

        <div class="mb-4">
            <label class="block text-gray-700">Name *</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}"
                   class="w-full border rounded px-3 py-2" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $company->email) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Address</label>
            <textarea name="address" class="w-full border rounded px-3 py-2" rows="3">{{ old('address', $company->address) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Tax ID</label>
            <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex justify-end">
            <a href="{{ route('companies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">Cancel</a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
        </div>
    </form>
</div>
@endsection
