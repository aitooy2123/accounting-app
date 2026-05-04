@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">

    {{-- HEADER --}}
    <div class="mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">แก้ไขเอกสารซื้อ</h1>
                <p class="text-sm text-gray-500 font-kanit mt-1">
                    เลขที่เอกสาร: <span class="font-mono font-bold text-purple-600">{{ $purchase->doc_no }}</span>
                </p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($purchase->status == 'ชำระแล้ว') bg-green-100 text-green-700
                    @elseif($purchase->status == 'ยกเลิก') bg-gray-100 text-gray-700
                    @else bg-yellow-100 text-yellow-700 @endif">
                    <i class="fas
                        @if($purchase->status == 'ชำระแล้ว') fa-check-circle
                        @elseif($purchase->status == 'ยกเลิก') fa-ban
                        @else fa-clock @endif mr-1"></i>
                    {{ $purchase->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        {{-- Hidden fields สำหรับเก็บค่าที่คำนวณ --}}
        <input type="hidden" name="vat" id="vat_amount" value="{{ old('vat', $purchase->vat) }}">
        <input type="hidden" name="total" id="total_amount" value="{{ old('total', $purchase->total) }}">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            {{-- SECTION: ข้อมูลเอกสาร --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-purple-600 uppercase tracking-wider mb-4 font-kanit">
                    <i class="fas fa-file-invoice mr-2"></i>ข้อมูลเอกสาร
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">
                            เลขที่เอกสาร <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="doc_no" value="{{ old('doc_no', $purchase->doc_no) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-mono font-bold text-purple-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('doc_no') border-red-300 @enderror"
                               required>
                        @error('doc_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">
                            วันที่เอกสาร <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="doc_date" value="{{ old('doc_date', $purchase->doc_date instanceof \Carbon\Carbon ? $purchase->doc_date->format('Y-m-d') : $purchase->doc_date) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('doc_date') border-red-300 @enderror">
                        @error('doc_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">
                            เจ้าหนี้/ผู้ขาย <span class="text-red-500">*</span>
                        </label>
                        <select name="supplier_id" required
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('supplier_id') border-red-300 @enderror">
                            <option value="">-- เลือกผู้ขาย --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->code }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">
                            สาขา <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id" required
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('branch_id') border-red-300 @enderror">
                            <option value="">-- เลือกสาขา --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id', $purchase->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->code }} - {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">
                            วันที่ครบกำหนด <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="due_date" value="{{ old('due_date', $purchase->due_date instanceof \Carbon\Carbon ? $purchase->due_date->format('Y-m-d') : $purchase->due_date) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('due_date') border-red-300 @enderror">
                        @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">สถานะ</label>
                        <select name="status" required
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-300 @enderror">
                            <option value="ค้างชำระ" {{ old('status', $purchase->status) == 'ค้างชำระ' ? 'selected' : '' }}>🔴 ค้างชำระ</option>
                            <option value="ชำระแล้ว" {{ old('status', $purchase->status) == 'ชำระแล้ว' ? 'selected' : '' }}>🟢 ชำระแล้ว</option>
                            <option value="ยกเลิก" {{ old('status', $purchase->status) == 'ยกเลิก' ? 'selected' : '' }}>⚫ ยกเลิก</option>
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- SECTION: ข้อมูลการเงิน --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-purple-600 uppercase tracking-wider mb-4 font-kanit">
                    <i class="fas fa-calculator mr-2"></i>ข้อมูลการเงิน
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">
                            ยอดเงินก่อนภาษี (฿) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="subtotal" id="subtotal" step="0.01" min="0"
                               value="{{ old('subtotal', $purchase->subtotal) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('subtotal') border-red-300 @enderror">
                        @error('subtotal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">
                            อัตราภาษี (%) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="vat_rate" id="vat_rate" step="0.01" min="0" max="100"
                               value="{{ old('vat_rate', $purchase->vat_rate) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('vat_rate') border-red-300 @enderror">
                        @error('vat_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">ยอดรวมสุทธิ (฿)</label>
                        <div class="w-full px-3 py-2.5 border border-gray-200 rounded-xl bg-purple-50 text-sm font-bold text-purple-700 font-mono">
                            <span id="totalDisplay">฿ {{ number_format($purchase->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <span class="text-gray-500 font-kanit">ภาษีมูลค่าเพิ่ม:</span>
                        <span id="vatDisplay" class="font-bold text-gray-700 font-mono ml-2">฿ {{ number_format($purchase->vat, 2) }}</span>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-3">
                        <span class="text-purple-600 font-kanit font-bold">รวมทั้งสิ้น:</span>
                        <span id="grandTotalDisplay" class="font-bold text-purple-700 font-mono ml-2 text-lg">฿ {{ number_format($purchase->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- SECTION: หมายเหตุ --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-purple-600 uppercase tracking-wider mb-4 font-kanit">
                    <i class="fas fa-sticky-note mr-2"></i>หมายเหตุ
                </h2>
                <textarea name="note" rows="3"
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('note') border-red-300 @enderror"
                          placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี)">{{ old('note', $purchase->note) }}</textarea>
                @error('note')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- SECTION: ข้อมูลการสร้าง/แก้ไข --}}
            <div class="pt-4 border-t border-gray-100 text-xs text-gray-400">
                <div class="flex justify-between">
                    <span>สร้างเมื่อ: {{ $purchase->created_at ? $purchase->created_at->format('d/m/Y H:i') : '-' }}</span>
                    <span>แก้ไขล่าสุด: {{ $purchase->updated_at ? $purchase->updated_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
                @if($purchase->created_by)
                    <span>สร้างโดย: {{ $purchase->creator->name ?? 'N/A' }}</span>
                @endif
            </div>
        </div>

        {{-- BUTTONS --}}
        <div class="flex justify-between items-center">
            <button type="button" onclick="confirmDelete()"
                    class="px-6 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold font-kanit hover:bg-red-700 transition-all shadow-md hover:shadow-lg">
                <i class="fas fa-trash-alt mr-2"></i>ลบเอกสาร
            </button>

            <div class="flex space-x-3">
                <a href="{{ route('purchases.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold font-kanit hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-xl text-sm font-bold font-kanit transition-all shadow-lg shadow-purple-200/50 hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>อัปเดตเอกสารซื้อ
                </button>
            </div>
        </div>
    </form>

    {{-- Hidden Delete Form --}}
    <form id="deleteForm" action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ฟังก์ชันคำนวณยอดรวม
    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;

        const vat = subtotal * (vatRate / 100);
        const total = subtotal + vat;

        const formatter = new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // อัปเดตการแสดงผล
        document.getElementById('vatDisplay').innerHTML = '฿ ' + formatter.format(vat);
        document.getElementById('totalDisplay').innerHTML = '฿ ' + formatter.format(total);
        document.getElementById('grandTotalDisplay').innerHTML = '฿ ' + formatter.format(total);

        // อัปเดตค่าใน hidden fields เพื่อส่งไป backend
        const vatInput = document.getElementById('vat_amount');
        const totalInput = document.getElementById('total_amount');

        if (vatInput) vatInput.value = vat.toFixed(2);
        if (totalInput) totalInput.value = total.toFixed(2);
    }

    // ฟังก์ชันยืนยันการลบ
    function confirmDelete() {
        Swal.fire({
            title: '<span class="font-kanit">ยืนยันการลบ?</span>',
            html: `
                <div class="font-kanit">
                    <p class="mb-2">คุณต้องการลบเอกสาร</p>
                    <p class="font-bold text-purple-600 text-lg">${escapeHtml(document.querySelector('input[name="doc_no"]').value)}</p>
                    <p class="text-red-500 text-sm mt-3">⚠️ การลบไม่สามารถกู้คืนได้</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>ลบเลย',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl font-kanit',
                title: 'text-xl',
                confirmButton: 'rounded-xl px-5 py-2 text-sm font-bold',
                cancelButton: 'rounded-xl px-5 py-2 text-sm font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบ...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('deleteForm').submit();
            }
        });
    }

    // ฟังก์ชัน escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ฟังก์ชัน validate ข้อมูล
    function validateForm() {
        const docDate = new Date(document.querySelector('input[name="doc_date"]').value);
        const dueDate = new Date(document.querySelector('input[name="due_date"]').value);
        const supplierSelect = document.querySelector('select[name="supplier_id"]');
        const branchSelect = document.querySelector('select[name="branch_id"]');
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;

        // Check if dates are valid
        if (isNaN(docDate.getTime()) || isNaN(dueDate.getTime())) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'กรุณากรอกวันที่ให้ถูกต้อง',
                confirmButtonColor: '#7C3AED',
                customClass: {
                    popup: 'rounded-2xl font-kanit'
                }
            });
            return false;
        }

        // เช็คผู้ขายและสาขา
        if (!supplierSelect.value) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'กรุณาเลือกผู้ขาย',
                confirmButtonColor: '#7C3AED',
                customClass: {
                    popup: 'rounded-2xl font-kanit'
                }
            });
            return false;
        }

        if (!branchSelect.value) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'กรุณาเลือกสาขา',
                confirmButtonColor: '#7C3AED',
                customClass: {
                    popup: 'rounded-2xl font-kanit'
                }
            });
            return false;
        }

        // เช็ควันที่
        dueDate.setHours(0, 0, 0, 0);
        docDate.setHours(0, 0, 0, 0);

        if (dueDate < docDate) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'วันที่ครบกำหนดต้องไม่ก่อนวันที่เอกสาร',
                confirmButtonColor: '#7C3AED',
                customClass: {
                    popup: 'rounded-2xl font-kanit'
                }
            });
            return false;
        }

        // เช็คยอดเงิน
        if (subtotal <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'ยอดเงินก่อนภาษีต้องมากกว่า 0',
                confirmButtonColor: '#7C3AED',
                customClass: {
                    popup: 'rounded-2xl font-kanit'
                }
            });
            return false;
        }

        // เช็ค VAT rate
        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;
        if (vatRate < 0 || vatRate > 100) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'อัตราภาษีต้องอยู่ระหว่าง 0-100%',
                confirmButtonColor: '#7C3AED',
                customClass: {
                    popup: 'rounded-2xl font-kanit'
                }
            });
            return false;
        }

        return true;
    }

    // ฟังก์ชันแสดง SweetAlert สำหรับ validation errors
    function showValidationErrors(errors) {
        let errorHtml = '<div class="text-left font-kanit">';
        for (let field in errors) {
            errorHtml += `<p class="text-red-600 text-sm mb-1">• ${errors[field][0]}</p>`;
        }
        errorHtml += '</div>';

        Swal.fire({
            icon: 'error',
            title: 'กรุณาตรวจสอบข้อมูล',
            html: errorHtml,
            confirmButtonColor: '#7C3AED',
            customClass: {
                popup: 'rounded-2xl font-kanit'
            }
        });
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calculation
        calculateTotal();

        // Set up event listeners for real-time calculation
        const subtotalField = document.getElementById('subtotal');
        const vatRateField = document.getElementById('vat_rate');

        if (subtotalField) {
            subtotalField.addEventListener('input', calculateTotal);
            subtotalField.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseFloat(this.value).toFixed(2);
                    calculateTotal();
                } else {
                    this.value = '0.00';
                    calculateTotal();
                }
            });
        }

        if (vatRateField) {
            vatRateField.addEventListener('input', calculateTotal);
            vatRateField.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseFloat(this.value).toFixed(2);
                    calculateTotal();
                } else {
                    this.value = '0.00';
                    calculateTotal();
                }
            });
            // แสดง placeholder สำหรับ VAT rate
            if (!vatRateField.value || vatRateField.value === '0') {
                vatRateField.placeholder = '7';
            }
        }

        // เพิ่ม keyboard shortcut (Ctrl+S)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                if (validateForm()) {
                    document.getElementById('purchaseForm').dispatchEvent(new Event('submit'));
                }
            }
        });

        // เพิ่ม keyboard shortcut for Cancel (Alt+Shift+C)
        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.shiftKey && e.key === 'C') {
                e.preventDefault();
                if (confirm('ต้องการยกเลิกการแก้ไขหรือไม่?')) {
                    window.location.href = document.querySelector('a[href*="purchases.index"]').href;
                }
            }
        });
    });

    // Submit form validation
    const form = document.getElementById('purchaseForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse mr-2"></i>กำลังบันทึก...';
            }

            return true;
        });
    }

    // ตรวจสอบการเปลี่ยนแปลงข้อมูล
    let formChanged = false;
    const formFields = document.getElementById('purchaseForm').querySelectorAll('input, select, textarea');

    formFields.forEach(field => {
        field.addEventListener('change', () => { formChanged = true; });
        field.addEventListener('keyup', () => { formChanged = true; });
    });

    // เตือนก่อนออกถ้ายังไม่ได้บันทึก
    let beforeUnloadHandler = function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'คุณยังไม่ได้บันทึกการเปลี่ยนแปลง ต้องการออกจากหน้านี้หรือไม่?';
            return e.returnValue;
        }
    };

    window.addEventListener('beforeunload', beforeUnloadHandler);

    // รีเซ็ต flag เมื่อ submit สำเร็จ
    form.addEventListener('submit', function() {
        formChanged = false;
        window.removeEventListener('beforeunload', beforeUnloadHandler);
    });

    // แสดง Server-side validation errors จาก Laravel
    @if($errors->any())
        const serverErrors = @json($errors->messages());
        showValidationErrors(serverErrors);
    @endif
</script>
@endpush

@push('styles')
<style>
    /* ปรับปรุงการแสดงผล */
    * {
        transition: all 0.1s ease;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 0.5;
    }

    input[type="number"]:hover::-webkit-inner-spin-button,
    input[type="number"]:hover::-webkit-outer-spin-button {
        opacity: 1;
    }

    .font-kanit {
        font-family: 'Kanit', sans-serif;
    }

    /* เพิ่ม animation สำหรับ loading */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .bg-white.rounded-2xl {
        animation: fadeIn 0.3s ease-out;
    }

    /* ปรับปรุง scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Disabled button style */
    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Form input transition */
    input, select, textarea {
        transition: all 0.2s ease;
    }

    /* Required field indicator */
    label span.text-red-500 {
        display: inline-block;
        margin-left: 2px;
    }

    /* Hover effects for buttons */
    .btn-hover-effect {
        transform: translateY(-1px);
    }

    /* Custom select styling */
    select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    /* Loading animation styles */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .loading-pulse {
        animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Focus visible for accessibility */
    :focus-visible {
        outline: 2px solid #7C3AED;
        outline-offset: 2px;
    }
</style>
@endpush

@endsection
