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
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all font-kanit"
                       placeholder="ค้นหาชื่อบริษัท อีเมล เบอร์โทร เลขภาษี...">
            </div>

            {{-- STATUS --}}
            <select name="status"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all font-kanit">
                <option value="">ทุกสถานะ</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>เปิดใช้งาน</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
            </select>

            {{-- BUTTON --}}
            <div class="flex space-x-2">
                <button type="submit"
                        class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all font-kanit">
                    กรองข้อมูล
                </button>

                <a href="{{ route('companies.index') }}"
                   class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all flex items-center justify-center">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-kanit">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">รหัสบริษัท</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">ชื่อบริษัท</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">ข้อมูลติดต่อ</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">เลขผู้เสียภาษี</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">สถานะ</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($companies as $company)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-blue-600 font-mono">{{ $company->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex items-center"><i class="far fa-envelope mr-2 text-gray-400 text-xs"></i> {{ $company->email ?? '-' }}</div>
                            <div class="flex items-center mt-1"><i class="fas fa-phone-alt mr-2 text-gray-400 text-xs"></i> {{ $company->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $company->tax_id ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $company->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <i class="fas {{ $company->is_active ? 'fa-check-circle' : 'fa-ban' }} mr-1 text-xs"></i>
                                {{ $company->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-1">
                                <a href="{{ route('companies.edit', $company) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="แก้ไข">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>
                                <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="ลบ")">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-building text-5xl mb-4 opacity-20"></i>
                                <span class="font-kanit text-sm">ยังไม่มีข้อมูลบริษัทในระบบ</span>
                                <a href="{{ route('companies.create') }}" class="mt-4 text-blue-600 text-xs font-bold hover:underline">เพิ่มบริษัทแรกของคุณที่นี่</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($companies->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $companies->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@session('scripts')
  @include('scripts.sweetalert2')
@endsession
