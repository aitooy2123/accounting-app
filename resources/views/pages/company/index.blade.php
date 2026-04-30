@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">จัดการบริษัท</h1>
            <p class="text-sm text-gray-500 font-kanit">จัดการข้อมูลบริษัทและสถานะการใช้งาน</p>
        </div>

        <a href="{{ route('companies.create') }}"
           class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200 font-kanit">
            <i class="fas fa-plus-circle mr-2"></i> เพิ่มบริษัทใหม่
        </a>
    </div>

    {{-- FILTER --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('companies.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- SEARCH --}}
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="ค้นหาชื่อบริษัท อีเมล เบอร์โทร เลขภาษี...">
            </div>

            {{-- STATUS --}}
            <select name="status"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 font-kanit">
                <option value="">ทุกสถานะ</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>เปิดใช้งาน</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
            </select>

            {{-- BUTTON --}}
            <div class="flex space-x-2">
                <button type="submit"
                        class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                    กรองข้อมูล
                </button>

                <a href="{{ route('companies.index') }}"
                   class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-kanit">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs text-gray-400">ID</th>
                        <th class="px-6 py-4 text-xs text-gray-400">ชื่อบริษัท</th>
                        <th class="px-6 py-4 text-xs text-gray-400">ติดต่อ</th>
                        <th class="px-6 py-4 text-xs text-gray-400">เลขภาษี</th>
                        <th class="px-6 py-4 text-xs text-gray-400 text-center">สถานะ</th>
                        <th class="px-6 py-4 text-xs text-gray-400 text-right">จัดการ</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($companies as $company)
                    <tr class="hover:bg-blue-50 transition">

                        <td class="px-6 py-4 text-sm">{{ $company->id }}</td>

                        <td class="px-6 py-4">
                            <div class="font-bold text-blue-600">{{ $company->name }}</div>
                            <div class="text-xs text-gray-400">{{ $company->email }}</div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $company->phone ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $company->tax_id ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full font-bold
                                {{ $company->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $company->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-1">

                                {{-- EDIT --}}
                                <a href="{{ route('companies.edit', $company) }}"
                                   class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>

                                {{-- DELETE --}}
                                <form action="{{ route('companies.destroy', $company) }}"
                                      method="POST"
                                      onsubmit="return confirm('ยืนยันการลบ?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 text-gray-400">
                            ไม่มีข้อมูลบริษัท
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($companies->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $companies->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
