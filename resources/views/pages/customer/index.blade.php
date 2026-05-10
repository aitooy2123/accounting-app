@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">จัดการลูกค้า</h1>
            <p class="text-sm text-gray-500 font-kanit">จัดการข้อมูลลูกค้าและติดตามสถานะการใช้งาน</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- BULK DELETE BUTTON - ซ่อนไว้ก่อนจนกว่าจะมีการเลือก --}}
            <button type="button"
                    id="bulkDeleteBtn"
                    class="hidden inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-red-200/50 font-kanit"
                    onclick="bulkDelete()">
                <i class="fas fa-trash-alt mr-2"></i>
                <span id="bulkDeleteText">ลบที่เลือก (0)</span>
            </button>


            {{-- ปุ่ม Import --}}
            <button onclick="openImportModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-green-200/50 font-kanit">
                <i class="fas fa-file-excel mr-2"></i> นำเข้าข้อมูลลูกค้า
            </button>

            <a href="{{ route('customers.template') }}"
               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-gray-200/50 font-kanit">
                <i class="fas fa-download mr-2"></i> ดาวน์โหลด Template
            </a>
                 <a href="{{ route('customers.create') }}"
               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-blue-200/50 hover:shadow-blue-300/50 transform hover:-translate-y-0.5 font-kanit">
                <i class="fas fa-plus-circle mr-2"></i> เพิ่มลูกค้าใหม่
            </a>

        </div>

    </div>

    {{-- SELECTION BAR --}}
    <div id="selectionBar" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center justify-between">
        <div class="flex items-center text-blue-700 font-kanit">
            <i class="fas fa-check-circle mr-2"></i>
            <span>เลือก <strong id="selectedCountDisplay">0</strong> รายการ</span>
        </div>
        <button type="button"
                onclick="clearSelection()"
                class="text-sm text-blue-600 hover:text-blue-800 font-kanit underline">
            ยกเลิกการเลือก
        </button>
    </div>

    {{-- FILTER --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('customers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-kanit"
                       placeholder="ค้นหารหัส ชื่อลูกค้า อีเมล เบอร์โทร บริษัท หรือสาขา...">
            </div>

            <select name="status" class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-kanit">
                <option value="">ทุกสถานะ</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>เปิดใช้งาน</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
            </select>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 font-kanit">
                    <i class="fas fa-filter mr-2"></i>กรองข้อมูล
                </button>
                <a href="{{ route('customers.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-kanit">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        {{-- CHECKBOX COLUMN HEADER --}}
                        <th class="px-6 py-4 w-12">
                            <div class="flex items-center">
                                <input type="checkbox"
                                       id="selectAllCheckbox"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                            </div>
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">รหัสลูกค้า</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ชื่อลูกค้า</th>
                        {{-- <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">เบอร์โทร</th> --}}
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">บริษัท/สาขา</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">สถานะ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-blue-50/30 transition-all duration-200 group {{ $customer->is_active ? '' : 'opacity-60' }}">
                        {{-- CHECKBOX COLUMN --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <input type="checkbox"
                                       class="customer-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                       value="{{ $customer->id }}"
                                       data-customer-id="{{ $customer->id }}"
                                       data-customer-code="{{ $customer->code }}"
                                       data-customer-name="{{ $customer->name }}">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-blue-600 font-mono bg-blue-50 px-2 py-1 rounded-lg">{{ $customer->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                                    {{ mb_substr($customer->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-semibold text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-xs text-gray-400">เลขผู้เสียภาษี: {{ $customer->tax_id ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        {{-- <td class="px-6 py-4 text-sm text-gray-600">
                            @if($customer->phone)
                                <div class="flex items-center">
                                    <i class="fas fa-phone-alt text-green-400 mr-2 text-xs w-4"></i>
                                    {{ $customer->phone }}
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td> --}}
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div>{{ $customer->company->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $customer->branch->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       class="sr-only peer toggle-status"
                                       data-customer-id="{{ $customer->id }}"
                                       {{ $customer->is_active ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-1">
                                <a href="{{ route('customers.show', $customer) }}"
                                   class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200"
                                   title="รายละเอียด">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('customers.edit', $customer) }}"
                                   class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                   title="แก้ไข">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </a>

                                <button type="button"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 delete-customer"
                                        data-customer-id="{{ $customer->id }}"
                                        data-customer-code="{{ $customer->code }}"
                                        data-customer-name="{{ $customer->name }}"
                                        title="ลบ">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-4xl text-gray-300"></i>
                                </div>
                                <span class="font-kanit text-gray-500 font-medium">ยังไม่มีข้อมูลลูกค้าในระบบ</span>
                                <a href="{{ route('customers.create') }}"
                                   class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-all duration-300">
                                    <i class="fas fa-plus mr-2"></i>เพิ่มลูกค้ารายแรกของคุณที่นี่
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $customers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- IMPORT MODAL --}}
<div id="importModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-xl">
        <h2 class="text-lg font-bold mb-4 font-kanit">นำเข้าข้อมูลลูกค้า</h2>

        <form action="{{ route('customers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" name="file"
                   class="w-full border border-gray-200 rounded-xl p-2 mb-4"
                   accept=".xlsx,.xls,.csv" required>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeImportModal()"
                        class="px-4 py-2 bg-gray-100 rounded-lg font-kanit">ยกเลิก</button>

                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-kanit">อัปโหลด</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @include('scripts.sweetalert2')
@endpush

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============= SESSION ALERTS =============

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    popup: 'font-kanit'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: "{{ session('error') }}",
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'font-kanit',
                    confirmButton: 'bg-blue-600 text-white px-6 py-2 rounded-lg'
                }
            });
        @endif

        // ============= BULK DELETE FUNCTIONALITY =============

        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteText = document.getElementById('bulkDeleteText');
        const selectionBar = document.getElementById('selectionBar');
        const selectedCountDisplay = document.getElementById('selectedCountDisplay');

        function updateSelectionUI() {
            const checkedCheckboxes = document.querySelectorAll('.customer-checkbox:checked');
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

            const allCheckboxes = document.querySelectorAll('.customer-checkbox');
            const allChecked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        }

        window.clearSelection = function() {
            customerCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            updateSelectionUI();
        };

        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            customerCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectionUI();
        });

        customerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectionUI);
        });

        // Bulk Delete Function
        window.bulkDelete = function() {
            const checkedCheckboxes = document.querySelectorAll('.customer-checkbox:checked');
            const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value);
            const selectedCustomers = Array.from(checkedCheckboxes).map(cb => ({
                code: cb.dataset.customerCode,
                name: cb.dataset.customerName
            }));

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกลูกค้า',
                    text: 'คุณต้องเลือกอย่างน้อย 1 รายการเพื่อลบ',
                    customClass: {
                        popup: 'font-kanit'
                    }
                });
                return;
            }

            const customerListHtml = selectedCustomers.slice(0, 5).map(cust =>
                `<li class="text-sm flex items-center">
                    <span class="font-mono font-bold text-blue-600 w-20">${cust.code}</span>
                    <span class="text-gray-600">${cust.name}</span>
                </li>`
            ).join('');

            const moreCount = selectedCustomers.length > 5 ?
                `<li class="text-sm text-gray-400 italic pl-5">...และอีก ${selectedCustomers.length - 5} รายการ</li>` : '';

            Swal.fire({
                title: 'ยืนยันการลบหลายรายการ',
                html: `
                    <div class="text-left">
                        <p class="mb-3">คุณต้องการลบข้อมูลลูกค้า <strong>${selectedIds.length} รายการ</strong> ใช่หรือไม่?</p>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                            <ul class="space-y-1.5">
                                ${customerListHtml}
                                ${moreCount}
                            </ul>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-red-600 text-sm font-bold mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>คำเตือน
                            </p>
                            <ul class="text-red-500 text-sm list-disc list-inside space-y-1">
                                <li>การกระทำนี้ไม่สามารถย้อนกลับได้</li>
                                <li>ข้อมูลที่เกี่ยวข้องทั้งหมดจะถูกลบออกจากระบบ</li>
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
                        html: 'กำลังลบข้อมูลลูกค้าที่เลือก กรุณารอสักครู่',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        customClass: {
                            popup: 'font-kanit'
                        }
                    });

                    fetch('{{ route("customers.bulk-delete") }}', {
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
                                text: data.message || `ลบข้อมูลลูกค้า ${selectedIds.length} รายการเรียบร้อยแล้ว`,
                                timer: 3000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'font-kanit'
                                }
                            });
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบ');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: error.message || 'ไม่สามารถลบข้อมูลลูกค้าได้ กรุณาลองใหม่อีกครั้ง',
                            confirmButtonText: 'ตกลง',
                            customClass: {
                                popup: 'font-kanit'
                            }
                        });
                    });
                }
            });
        };

        // ============= TOGGLE STATUS FUNCTIONALITY =============

        document.querySelectorAll('.toggle-status').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const customerId = this.dataset.customerId;
                const isActive = this.checked;

                fetch(`/customers/${customerId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ is_active: isActive })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัปเดตสถานะเรียบร้อย',
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'font-kanit'
                            }
                        });
                        setTimeout(() => location.reload(), 1600);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !isActive;
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถอัปเดตสถานะได้',
                        customClass: {
                            popup: 'font-kanit'
                        }
                    });
                });
            });
        });

        // ============= SINGLE DELETE FUNCTIONALITY =============

        document.querySelectorAll('.delete-customer').forEach(button => {
            button.addEventListener('click', function() {
                const customerId = this.dataset.customerId;
                const customerCode = this.dataset.customerCode;
                const customerName = this.dataset.customerName;

                Swal.fire({
                    title: 'ยืนยันการลบข้อมูลลูกค้า?',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">คุณต้องการลบข้อมูลลูกค้า <strong>${customerCode} - ${customerName}</strong> ใช่หรือไม่?</p>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-red-600 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span class="text-red-500 text-sm">ข้อมูลที่เกี่ยวข้องทั้งหมดจะถูกลบ การกระทำนี้ไม่สามารถย้อนกลับได้</span>
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
                        form.action = `/customers/${customerId}`;
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

    // Import Modal Functions
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
        document.getElementById('importModal').classList.add('flex');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('importModal').classList.remove('flex');
    }
</script>

<style>
    input[type="checkbox"] {
        cursor: pointer;
    }

    input[type="checkbox"]:checked {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }

    tr:has(.customer-checkbox:checked) {
        background-color: #EFF6FF;
    }

    .toggle-status + div {
        transition: all 0.3s ease;
    }

    .toggle-status:checked + div {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
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
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z' clip-rule='evenodd'/%3E%3Csvg%3E");
    }
</style>
@endsection
