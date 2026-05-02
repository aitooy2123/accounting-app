@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">ผังบัญชี (Chart of Accounts)</h1>
            <p class="text-sm text-gray-500 font-kanit">จัดการรหัสบัญชีและโครงสร้างบัญชีทั้งหมดในระบบ</p>
        </div>

        <a href="{{ route('accounts.create') }}"
           class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-emerald-200 font-kanit">
            <i class="fas fa-plus-circle mr-2"></i> เพิ่มรหัสบัญชี
        </a>
    </div>

    {{-- FILTER --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('accounts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- SEARCH --}}
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 transition-all font-kanit"
                       placeholder="ค้นหาชื่อบัญชี หรือ รหัสบัญชี...">
            </div>

            {{-- CATEGORY FILTER --}}
            <select name="category"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 transition-all font-kanit">
                <option value="">ทุกหมวดบัญชี</option>
                <option value="asset" {{ request('category') == 'asset' ? 'selected' : '' }}>1. สินทรัพย์</option>
                <option value="liability" {{ request('category') == 'liability' ? 'selected' : '' }}>2. หนี้สิน</option>
                <option value="equity" {{ request('category') == 'equity' ? 'selected' : '' }}>3. ส่วนของเจ้าของ</option>
                <option value="revenue" {{ request('category') == 'revenue' ? 'selected' : '' }}>4. รายได้</option>
                <option value="expense" {{ request('category') == 'expense' ? 'selected' : '' }}>5. ค่าใช้จ่าย</option>
            </select>

            {{-- BUTTONS --}}
            <div class="flex space-x-2">
                <button type="submit"
                        class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all font-kanit">
                    กรองข้อมูล
                </button>
                <a href="{{ route('accounts.index') }}"
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
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">รหัสบัญชี</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">ชื่อบัญชี</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">หมวด</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">ประเภท</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($accounts as $account)
                    <tr class="hover:bg-emerald-50/30 transition-colors group {{ $account->is_group ? 'bg-gray-50/30' : '' }}">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold {{ $account->is_group ? 'text-gray-900' : 'text-emerald-600' }} font-mono">
                                {{ $account->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm {{ $account->is_group ? 'font-bold text-gray-900' : 'font-medium text-gray-700' }}">
                                {{-- ดันขวาเล็กน้อยถ้าเป็นบัญชีย่อย --}}
                                @if($account->parent_id) <span class="ml-4 text-gray-300">|—</span> @endif
                                {{ $account->name_th }}
                            </div>
                            @if($account->name_en)
                                <div class="text-[10px] text-gray-400 {{ $account->parent_id ? 'ml-10' : '' }}">
                                    {{ $account->name_en }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeColors = [
                                    'asset' => 'bg-blue-100 text-blue-700',
                                    'liability' => 'bg-red-100 text-red-700',
                                    'equity' => 'bg-purple-100 text-purple-700',
                                    'revenue' => 'bg-emerald-100 text-emerald-700',
                                    'expense' => 'bg-orange-100 text-orange-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $badgeColors[$account->category] ?? 'bg-gray-100' }}">
                                {{ strtoupper($account->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($account->is_group)
                                <span class="text-[11px] font-bold text-gray-500 bg-gray-200 px-2 py-1 rounded-md">บัญชีคุม</span>
                            @else
                                <span class="text-[11px] font-bold text-emerald-600 border border-emerald-200 px-2 py-1 rounded-md">บัญชีย่อย</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-1">
                                <a href="{{ route('accounts.edit', $account) }}"
                                   class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>
                                <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline-block delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all delete-btn">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-invoice-dollar text-5xl mb-4 opacity-20"></i>
                                <span class="font-kanit text-sm">ยังไม่มีผังบัญชีในระบบ</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($accounts->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $accounts->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- SCRIPTS (ใช้เหมือนเดิม) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false,
                fontFamily: 'Kanit'
            });
        @endif

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: 'หากลบรหัสบัญชีนี้ ข้อมูลที่เกี่ยวข้องอาจได้รับผลกระทบ',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'ยืนยันการลบ',
                    cancelButtonText: 'ยกเลิก',
                    fontFamily: 'Kanit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
