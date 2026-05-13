@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">สมุดรายวันจ่าย (PV)</h1>
            <p class="text-sm text-gray-500 font-kanit">จัดการรายการจ่ายทั้งหมดและบันทึกการชำระเงิน</p>
        </div>

        <div class="flex items-center space-x-3">
            <button type="button" id="bulkDeleteBtn"
                    class="hidden inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-red-200/50 font-kanit"
                    onclick="bulkDelete()">
                <i class="fas fa-trash-alt mr-2"></i>
                <span id="bulkDeleteText">ลบที่เลือก (0)</span>
            </button>

            <a href="{{ route('pv.create') }}"
               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-200/50 font-kanit">
                <i class="fas fa-plus-circle mr-2"></i> สร้าง PV
            </a>
        </div>
    </div>

    {{-- SELECTION BAR --}}
    <div id="selectionBar" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center justify-between">
        <div class="flex items-center text-blue-700 font-kanit">
            <i class="fas fa-check-circle mr-2"></i>
            <span>เลือก <strong id="selectedCountDisplay">0</strong> รายการ</span>
        </div>
        <button type="button" onclick="clearSelection()"
                class="text-sm text-blue-600 hover:text-blue-800 font-kanit underline">
            ยกเลิกการเลือก
        </button>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('pv.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 font-kanit"
                       placeholder="ค้นหาเลขที่ PV, รายละเอียด...">
            </div>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 font-kanit"
                       placeholder="วันที่เริ่มต้น">
            </div>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 font-kanit"
                       placeholder="วันที่สิ้นสุด">
            </div>

            <div class="flex space-x-2 md:col-span-4">
                <button type="submit" class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all font-kanit">
                    <i class="fas fa-filter mr-2"></i>กรองข้อมูล
                </button>
                <a href="{{ route('pv.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all flex items-center justify-center">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                <span class="text-green-700 font-kanit">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                <span class="text-red-700 font-kanit">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-kanit">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 w-12">
                            <input type="checkbox" id="selectAllCheckbox"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">เลขที่ PV</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">วันที่</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">รายละเอียด</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">ยอดรวม</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">สถานะ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vouchers as $pv)
                    <tr class="hover:bg-blue-50/30 transition-all duration-200 group">
                        <td class="px-6 py-4">
                            <input type="checkbox"
                                   class="pv-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                                   value="{{ $pv->id }}"
                                   data-pv-id="{{ $pv->id }}"
                                   data-pv-no="{{ $pv->pv_no }}">
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-blue-600 font-mono bg-blue-50 px-2 py-1 rounded-lg">{{ $pv->pv_no }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $pv->pv_date instanceof \Carbon\Carbon ? $pv->pv_date->format('d/m/Y') : \Carbon\Carbon::parse($pv->pv_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-800 max-w-md">
                                {{ Str::limit($pv->note ?? '-', 80) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            ฿ {{ number_format($pv->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold
                                {{ ($pv->status ?? 'ปกติ') == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : (($pv->status ?? 'ปกติ') == 'ยกเลิก' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                <i class="fas {{ ($pv->status ?? 'ปกติ') == 'ชำระแล้ว' ? 'fa-check-circle' : (($pv->status ?? 'ปกติ') == 'ยกเลิก' ? 'fa-times-circle' : 'fa-clock') }} mr-1 text-xs"></i>
                                {{ $pv->status ?? 'ปกติ' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-1">
                                <a href="{{ route('pv.show', $pv->id) }}"
                                   class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                   title="ดูรายละเอียด">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('pv.edit', $pv->id) }}"
                                   class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200"
                                   title="แก้ไข">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </a>
                                {{-- <button type="button"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 delete-pv"
                                        data-pv-id="{{ $pv->id }}"
                                        data-pv-no="{{ $pv->pv_no }}"
                                        title="ลบ">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button> --}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-receipt text-4xl text-gray-300"></i>
                                </div>
                                <span class="font-kanit text-gray-500 font-medium">ยังไม่มีรายการ PV ในระบบ</span>
                                <a href="{{ route('pv.create') }}"
                                   class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-all duration-300">
                                    <i class="fas fa-plus mr-2"></i>สร้าง PV แรกของคุณที่นี่
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($vouchers->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $vouchers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============= SELECTION & BULK DELETE =============

        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const pvCheckboxes = document.querySelectorAll('.pv-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteText = document.getElementById('bulkDeleteText');
        const selectionBar = document.getElementById('selectionBar');
        const selectedCountDisplay = document.getElementById('selectedCountDisplay');

        function updateSelectionUI() {
            const checkedCheckboxes = document.querySelectorAll('.pv-checkbox:checked');
            const count = checkedCheckboxes.length;

            selectedCountDisplay.textContent = count;

            if (count > 0) {
                bulkDeleteBtn.classList.remove('hidden');
                selectionBar.classList.remove('hidden');
                bulkDeleteText.textContent = `ลบที่เลือก (${count})`;
            } else {
                bulkDeleteBtn.classList.add('hidden');
                selectionBar.classList.add('hidden');
            }

            const allCheckboxes = document.querySelectorAll('.pv-checkbox');
            const allChecked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
            }
        }

        // Clear all selections
        window.clearSelection = function() {
            pvCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateSelectionUI();
        };

        // Select All / Deselect All
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                pvCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateSelectionUI();
            });
        }

        // Individual checkbox change
        pvCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectionUI);
        });

        // Bulk Delete Function
        window.bulkDelete = function() {
            const checkedCheckboxes = document.querySelectorAll('.pv-checkbox:checked');
            const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value);
            const selectedPvNos = Array.from(checkedCheckboxes).map(cb => cb.dataset.pvNo);

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกเอกสาร',
                    text: 'คุณต้องเลือกอย่างน้อย 1 รายการเพื่อลบ',
                    customClass: { popup: 'font-kanit' }
                });
                return;
            }

            const pvListHtml = selectedPvNos.slice(0, 5).map(pv =>
                `<li class="text-sm"><i class="fas fa-receipt mr-2 text-red-400"></i>${pv}</li>`
            ).join('');

            const moreCount = selectedPvNos.length > 5 ?
                `<li class="text-sm text-gray-400 italic pl-5">...และอีก ${selectedPvNos.length - 5} รายการ</li>` : '';

            Swal.fire({
                title: 'ยืนยันการลบหลายรายการ',
                html: `
                    <div class="text-left">
                        <p class="mb-3">คุณต้องการลบ PV ทั้งหมด <strong>${selectedIds.length} รายการ</strong> ใช่หรือไม่?</p>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                            <ul class="space-y-1">
                                ${pvListHtml}
                                ${moreCount}
                            </ul>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-red-600 text-sm font-bold mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>คำเตือน
                            </p>
                            <ul class="text-red-500 text-sm list-disc list-inside space-y-1">
                                <li>การกระทำนี้ไม่สามารถย้อนกลับได้</li>
                                <li>ข้อมูลทั้งหมดที่เกี่ยวข้องจะถูกลบออกจากระบบ</li>
                                <li>กรุณาตรวจสอบรายการที่เลือกให้แน่ใจก่อนลบ</li>
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: `<i class="fas fa-trash-alt mr-2"></i>ลบ ${selectedIds.length} รายการ`,
                cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
                reverseButtons: true,
                customClass: {
                    popup: 'font-kanit rounded-2xl',
                    confirmButton: 'px-6 py-2.5 rounded-xl font-bold',
                    cancelButton: 'px-6 py-2.5 rounded-xl font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'กำลังดำเนินการ...',
                        html: 'กำลังลบ PV ที่เลือก กรุณารอสักครู่',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: { popup: 'font-kanit' }
                    });

                    fetch('{{ route("pv.bulk-delete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ!',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false,
                                customClass: { popup: 'font-kanit' }
                            });
                            setTimeout(() => { window.location.reload(); }, 1500);
                        } else {
                            throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบ');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: error.message,
                            confirmButtonText: 'ตกลง',
                            customClass: { popup: 'font-kanit' }
                        });
                    });
                }
            });
        };

        // ============= SINGLE DELETE =============

        document.querySelectorAll('.delete-pv').forEach(button => {
            button.addEventListener('click', function() {
                const pvId = this.dataset.pvId;
                const pvNo = this.dataset.pvNo;

                Swal.fire({
                    title: 'ยืนยันการลบเอกสาร?',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">คุณต้องการลบ PV <strong>${pvNo}</strong> ใช่หรือไม่?</p>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-red-600 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span class="text-red-500 text-sm">การกระทำนี้ไม่สามารถย้อนกลับได้</span>
                                </p>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>ลบ',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
                    reverseButtons: true,
                    customClass: {
                        popup: 'font-kanit rounded-2xl',
                        confirmButton: 'px-6 py-2.5 rounded-xl font-bold',
                        cancelButton: 'px-6 py-2.5 rounded-xl font-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ url('/pv') }}/${pvId}`;
                        form.innerHTML = `
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Initial UI state
        updateSelectionUI();
    });
</script>

<style>
    input[type="checkbox"] {
        cursor: pointer;
    }

    input[type="checkbox"]:checked {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }

    tr:has(.pv-checkbox:checked) {
        background-color: #EFF6FF;
    }

    .swal2-popup {
        border-radius: 1rem !important;
        padding: 2rem !important;
    }

    .swal2-title {
        font-size: 1.5rem !important;
    }

    .swal2-confirm, .swal2-cancel {
        font-family: 'Kanit', sans-serif !important;
    }

    #bulkDeleteBtn, #selectionBar {
        transition: all 0.3s ease-in-out;
    }

    #selectAllCheckbox:indeterminate {
        background-color: #3B82F6;
        border-color: #3B82F6;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z' clip-rule='evenodd'/%3E%3C/svg%3E");
    }
</style>
@endsection
