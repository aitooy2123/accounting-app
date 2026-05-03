@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">ผังบัญชี (Chart of Accounts)</h1>
            <p class="text-sm text-gray-500 font-kanit">จัดการรหัสบัญชีและโครงสร้างบัญชีทั้งหมดในระบบ</p>
        </div>

        <div class="flex items-center space-x-3">
            {{-- BULK DELETE BUTTON - ซ่อนไว้ก่อนจนกว่าจะมีการเลือก --}}
            <button type="button"
                    id="bulkDeleteBtn"
                    class="hidden inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-red-200/50 font-kanit"
                    onclick="bulkDelete()">
                <i class="fas fa-trash-alt mr-2"></i>
                <span id="bulkDeleteText">ลบที่เลือก (0)</span>
            </button>

            <a href="{{ route('accounts.create') }}"
               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-emerald-200/50 hover:shadow-emerald-300/50 transform hover:-translate-y-0.5 font-kanit">
                <i class="fas fa-plus-circle mr-2"></i> เพิ่มรหัสบัญชี
            </a>
        </div>
    </div>

    {{-- SELECTION BAR --}}
    <div id="selectionBar" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-4 flex items-center justify-between">
        <div class="flex items-center text-emerald-700 font-kanit">
            <i class="fas fa-check-circle mr-2"></i>
            <span>เลือก <strong id="selectedCountDisplay">0</strong> รายการ</span>
        </div>
        <button type="button"
                onclick="clearSelection()"
                class="text-sm text-emerald-600 hover:text-emerald-800 font-kanit underline">
            ยกเลิกการเลือก
        </button>
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
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all font-kanit"
                       placeholder="ค้นหาชื่อบัญชี หรือ รหัสบัญชี...">
            </div>

            {{-- CATEGORY FILTER --}}
            <select name="category"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all font-kanit">
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
                        class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 font-kanit">
                    <i class="fas fa-filter mr-2"></i>กรองข้อมูล
                </button>
                <a href="{{ route('accounts.index') }}"
                   class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all duration-300 flex items-center justify-center">
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
                                       class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 cursor-pointer">
                            </div>
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">รหัสบัญชี</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ชื่อบัญชี</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">หมวด</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">ประเภท</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">สถานะ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $account)
                    <tr class="hover:bg-emerald-50/30 transition-all duration-200 group {{ $account->is_group ? 'bg-gray-50/30' : '' }} {{ $account->is_active ? '' : 'opacity-60' }}">
                        {{-- CHECKBOX COLUMN --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <input type="checkbox"
                                       class="account-checkbox w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 cursor-pointer"
                                       value="{{ $account->id }}"
                                       data-account-id="{{ $account->id }}"
                                       data-account-code="{{ $account->code }}"
                                       data-account-name="{{ $account->name_th }}">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold {{ $account->is_group ? 'text-gray-900' : 'text-emerald-600' }} font-mono">
                                {{ $account->code }}
                            </span>
                            @if($account->is_group)
                                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-200 text-gray-600">
                                    <i class="fas fa-folder mr-1"></i>กลุ่ม
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm {{ $account->is_group ? 'font-bold text-gray-900' : 'font-medium text-gray-700' }}">
                                {{-- ดันขวาเล็กน้อยถ้าเป็นบัญชีย่อย --}}
                                @if($account->parent_id)
                                    <span class="ml-4 text-gray-300">└</span>
                                @endif
                                {{ $account->name_th }}
                            </div>
                            @if($account->name_en)
                                <div class="text-[10px] text-gray-400 {{ $account->parent_id ? 'ml-8' : '' }}">
                                    {{ $account->name_en }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeColors = [
                                    'asset' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'liability' => 'bg-red-100 text-red-700 border-red-200',
                                    'equity' => 'bg-purple-100 text-purple-700 border-purple-200',
                                    'revenue' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'expense' => 'bg-orange-100 text-orange-700 border-orange-200',
                                ];
                                $categoryLabels = [
                                    'asset' => 'สินทรัพย์',
                                    'liability' => 'หนี้สิน',
                                    'equity' => 'ส่วนของเจ้าของ',
                                    'revenue' => 'รายได้',
                                    'expense' => 'ค่าใช้จ่าย',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $badgeColors[$account->category] ?? 'bg-gray-100 border-gray-200 text-gray-700' }}">
                                <i class="fas fa-tag mr-1 text-[8px]"></i>
                                {{ $categoryLabels[$account->category] ?? strtoupper($account->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($account->is_group)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                    <i class="fas fa-layer-group mr-1 text-[9px]"></i>บัญชีคุม
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                    <i class="fas fa-file-alt mr-1 text-[9px]"></i>บัญชีย่อย
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       class="sr-only peer toggle-status"
                                       data-account-id="{{ $account->id }}"
                                       {{ $account->is_active ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                                </div>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-1">
                                <a href="{{ route('accounts.edit', $account) }}"
                                   class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200"
                                   title="แก้ไข">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </a>
                                <button type="button"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 delete-account"
                                        data-account-id="{{ $account->id }}"
                                        data-account-code="{{ $account->code }}"
                                        data-account-name="{{ $account->name_th }}"
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
                                    <i class="fas fa-file-invoice-dollar text-4xl text-gray-300"></i>
                                </div>
                                <span class="font-kanit text-gray-500 font-medium">ยังไม่มีผังบัญชีในระบบ</span>
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

{{-- SCRIPTS --}}
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
                    confirmButton: 'bg-emerald-600 text-white px-6 py-2 rounded-lg'
                }
            });
        @endif

        // ============= BULK DELETE FUNCTIONALITY =============

        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const accountCheckboxes = document.querySelectorAll('.account-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteText = document.getElementById('bulkDeleteText');
        const selectionBar = document.getElementById('selectionBar');
        const selectedCountDisplay = document.getElementById('selectedCountDisplay');

        function updateSelectionUI() {
            const checkedCheckboxes = document.querySelectorAll('.account-checkbox:checked');
            const count = checkedCheckboxes.length;

            // Update selected count display
            selectedCountDisplay.textContent = count;

            // Show/hide bulk delete button and selection bar
            if (count > 0) {
                bulkDeleteBtn.classList.remove('hidden');
                selectionBar.classList.remove('hidden');
                bulkDeleteText.textContent = `ลบที่เลือก (${count})`;
            } else {
                bulkDeleteBtn.classList.add('hidden');
                selectionBar.classList.add('hidden');
            }

            // Update select all checkbox state
            const allCheckboxes = document.querySelectorAll('.account-checkbox');
            const allChecked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        }

        // Clear all selections
        window.clearSelection = function() {
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            updateSelectionUI();
        };

        // Select All / Deselect All
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectionUI();
        });

        // Individual checkbox change
        accountCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectionUI);
        });

        // Bulk Delete Function
        window.bulkDelete = function() {
            const checkedCheckboxes = document.querySelectorAll('.account-checkbox:checked');
            const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value);

            // Get selected account details for display
            const selectedAccounts = Array.from(checkedCheckboxes).map(cb => ({
                code: cb.dataset.accountCode,
                name: cb.dataset.accountName
            }));

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกรหัสบัญชี',
                    text: 'คุณต้องเลือกอย่างน้อย 1 รายการเพื่อลบ',
                    customClass: {
                        popup: 'font-kanit'
                    }
                });
                return;
            }

            // Create list of accounts to delete
            const accountListHtml = selectedAccounts.slice(0, 5).map(acc =>
                `<li class="text-sm flex items-center">
                    <span class="font-mono font-bold text-emerald-600 w-20">${acc.code}</span>
                    <span class="text-gray-600">${acc.name}</span>
                </li>`
            ).join('');

            const moreCount = selectedAccounts.length > 5 ?
                `<li class="text-sm text-gray-400 italic pl-5">...และอีก ${selectedAccounts.length - 5} รายการ</li>` : '';

            Swal.fire({
                title: 'ยืนยันการลบหลายรายการ',
                html: `
                    <div class="text-left">
                        <p class="mb-3">คุณต้องการลบบัญชีทั้งหมด <strong>${selectedIds.length} รายการ</strong> ใช่หรือไม่?</p>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                            <ul class="space-y-1.5">
                                ${accountListHtml}
                                ${moreCount}
                            </ul>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-red-600 text-sm font-bold mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>คำเตือน
                            </p>
                            <ul class="text-red-500 text-sm list-disc list-inside space-y-1">
                                <li>การกระทำนี้ไม่สามารถย้อนกลับได้</li>
                                <li>ข้อมูลที่เกี่ยวข้องทั้งหมดจะได้รับผลกระทบ</li>
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
                    // Show loading state
                    Swal.fire({
                        title: 'กำลังดำเนินการ...',
                        html: 'กำลังลบบัญชีที่เลือก กรุณารอสักครู่',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        customClass: {
                            popup: 'font-kanit'
                        }
                    });

                    // Send bulk delete request
                    fetch('{{ route("accounts.bulk-delete") }}', {
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
                                text: data.message || `ลบบัญชี ${selectedIds.length} รายการเรียบร้อยแล้ว`,
                                timer: 3000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'font-kanit'
                                }
                            });
                            // Reload page after success
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
                            text: error.message || 'ไม่สามารถลบบัญชีได้ กรุณาลองใหม่อีกครั้ง',
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
                const accountId = this.dataset.accountId;
                const isActive = this.checked;

                fetch(`/accounts/${accountId}/toggle-status`, {
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
                    this.checked = !isActive; // Revert toggle
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

        document.querySelectorAll('.delete-account').forEach(button => {
            button.addEventListener('click', function() {
                const accountId = this.dataset.accountId;
                const accountCode = this.dataset.accountCode;
                const accountName = this.dataset.accountName;

                Swal.fire({
                    title: 'ยืนยันการลบบัญชี?',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">คุณต้องการลบบัญชี <strong>${accountCode} - ${accountName}</strong> ใช่หรือไม่?</p>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-red-600 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span class="text-red-500 text-sm">ข้อมูลที่เกี่ยวข้องอาจได้รับผลกระทบ การกระทำนี้ไม่สามารถย้อนกลับได้</span>
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
                        form.action = `/accounts/${accountId}`;
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
        background-color: #059669;
        border-color: #059669;
    }

    tr:has(.account-checkbox:checked) {
        background-color: #ECFDF5;
    }

    .toggle-status + div {
        transition: all 0.3s ease;
    }

    .toggle-status:checked + div {
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.3);
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

    /* Checkbox indeterminate state */
    #selectAllCheckbox:indeterminate {
        background-color: #059669;
        border-color: #059669;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z' clip-rule='evenodd'/%3E%3C/svg%3E");
    }
</style>
@endsection
