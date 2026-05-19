@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">จัดการหัก ณ ที่จ่าย</h1>
            <p class="text-sm text-gray-500 font-kanit">บันทึกใบหัก ณ ที่จ่าย พร้อมอัตราและยอดเงิน</p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" id="bulkDeleteBtn"
                class="hidden inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-red-200/50 font-kanit"
                onclick="bulkDelete()">
                <i class="fas fa-trash-alt mr-2"></i>
                <span id="bulkDeleteText">ลบที่เลือก (0)</span>
            </button>
            <a href="{{ route('withholding-tax.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-blue-200/50">
                <i class="fas fa-plus-circle mr-2"></i> เพิ่มหัก ณ ที่จ่าย
            </a>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('withholding-tax.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    placeholder="ค้นหาเลขที่เอกสาร, เลขที่ใบกำกับภาษี หรือผู้จำหน่าย...">
            </div>
            <select name="company_id" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">-- ผู้จำหน่ายทั้งหมด --</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="from_date" value="{{ request('from_date') }}"
                class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="จากวันที่">
            <input type="date" name="to_date" value="{{ request('to_date') }}"
                class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="ถึงวันที่">
            <div class="flex space-x-2">
                <button type="submit"
                    class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition">
                    <i class="fas fa-filter mr-2"></i>กรอง
                </button>
                <a href="{{ route('withholding-tax.index') }}"
                    class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- SELECTION BAR --}}
    <div id="selectionBar" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center justify-between">
        <div class="flex items-center text-blue-700 font-kanit">
            <i class="fas fa-check-circle mr-2"></i>
            <span>เลือก <strong id="selectedCountDisplay">0</strong> รายการ</span>
        </div>
        <button type="button" onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800 underline">
            ยกเลิกการเลือก
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-kanit">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 w-12">
                            <input type="checkbox" id="selectAllCheckbox"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">เลขที่ใบหัก ณ ที่จ่าย</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">วันที่</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">ผู้จำหน่าย</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">เลขที่ใบกำกับภาษี</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">ยอดก่อนหัก</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">อัตรา (%)</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">จำนวนภาษีหัก ณ ที่จ่าย</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($withholdingTaxes as $item)
                    <tr class="hover:bg-blue-50/30 transition-all duration-200 group" data-id="{{ $item->id }}">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="wt-checkbox w-4 h-4 text-blue-600 rounded"
                                value="{{ $item->id }}" data-wt-id="{{ $item->id }}" data-wt-name="{{ $item->withholding_no }}">
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-purple-600 font-mono bg-purple-50 px-2 py-1 rounded-lg">
                                {{ $item->withholding_no }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-700">
                            {{ $item->withholding_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-md">
                                    {{ mb_substr($item->company->name ?? '?', 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-semibold text-gray-900">{{ $item->company->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->invoice_no ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-800">
                            {{ number_format($item->tax_base, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-blue-600">
                            {{ $item->tax_rate }}%
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                            {{ number_format($item->tax_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-1">
                                <a href="{{ route('withholding-tax.show', $item) }}" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg" title="ดูรายละเอียด">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('withholding-tax.edit', $item) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="แก้ไข">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </a>
                                <button type="button" class="delete-wt p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
                                    data-wt-id="{{ $item->id }}" data-wt-name="{{ $item->withholding_no }}" title="ลบ">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-percent text-4xl text-gray-300"></i>
                                </div>
                                <span class="font-kanit text-gray-500 font-medium">ยังไม่มีรายการหัก ณ ที่จ่าย</span>
                                <a href="{{ route('withholding-tax.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>สร้างรายการแรก
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($withholdingTaxes->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $withholdingTaxes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const selectAll = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.wt-checkbox');
    const bulkBtn = document.getElementById('bulkDeleteBtn');
    const bulkText = document.getElementById('bulkDeleteText');
    const selectionBar = document.getElementById('selectionBar');
    const selectedCountSpan = document.getElementById('selectedCountDisplay');

    // Update UI (selection bar, button text, selectAll state)
    function updateUI() {
        const checked = document.querySelectorAll('.wt-checkbox:checked');
        const count = checked.length;
        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkBtn.classList.remove('hidden');
            selectionBar.classList.remove('hidden');
            bulkText.textContent = `ลบที่เลือก (${count})`;
        } else {
            bulkBtn.classList.add('hidden');
            selectionBar.classList.add('hidden');
        }

        // Update select all checkbox state
        const allChecked = checkboxes.length > 0 && checked.length === checkboxes.length;
        selectAll.checked = allChecked;
        selectAll.indeterminate = (checked.length > 0 && checked.length < checkboxes.length);
    }

    // Clear all selections
    window.clearSelection = function() {
        checkboxes.forEach(cb => cb.checked = false);
        updateUI();
    };

    // Select all / none
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateUI();
    });

    // Individual checkbox change
    checkboxes.forEach(cb => cb.addEventListener('change', updateUI));

    // Initialize UI
    updateUI();

    // Bulk delete function
    window.bulkDelete = function() {
        const checkedBoxes = document.querySelectorAll('.wt-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        const names = Array.from(checkedBoxes).map(cb => cb.dataset.wtName);

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกรายการ',
                text: 'คุณต้องเลือกอย่างน้อย 1 รายการ',
                customClass: { popup: 'font-kanit' }
            });
            return;
        }

        // Build list of selected items (max 5)
        const listItems = names.slice(0, 5).map(n => `<li><i class="fas fa-receipt mr-2 text-red-400"></i>${escapeHtml(n)}</li>`).join('');
        const moreCount = names.length > 5 ? `<li class="text-gray-400">...อีก ${names.length - 5} รายการ</li>` : '';

        Swal.fire({
            title: 'ยืนยันการลบหลายรายการ',
            html: `<p>คุณต้องการลบ <strong>${ids.length} รายการ</strong> ต่อไปนี้ใช่หรือไม่?</p>
                    <ul class="text-left max-h-32 overflow-auto">${listItems}${moreCount}</ul>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: `ลบ ${ids.length} รายการ`,
            cancelButtonText: 'ยกเลิก',
            customClass: { popup: 'font-kanit' }
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบ...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch('{{ route("withholding-tax.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: err.message,
                        customClass: { popup: 'font-kanit' }
                    });
                });
            }
        });
    };

    // Single delete
    document.querySelectorAll('.delete-wt').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.wtId;
            const name = this.dataset.wtName;
            Swal.fire({
                title: 'ยืนยันการลบ',
                html: `เอกสาร <strong>${escapeHtml(name)}</strong> จะถูกลบอย่างถาวร`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'ลบเลย',
                cancelButtonText: 'ยกเลิก',
                customClass: { popup: 'font-kanit' }
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/withholding-tax/${id}`;
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">
                                      <input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Helper function to escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
});
document.querySelector('tbody').addEventListener('change', function(e) {
    if (e.target.classList.contains('wt-checkbox')) {
        updateUI();
    }
});>
@endpush

<style>
    input[type="checkbox"]:checked { background-color: #3B82F6; }
    tr:has(.wt-checkbox:checked) { background-color: #EFF6FF; }
    .swal2-popup { border-radius: 1rem; }
</style>
@endsection
