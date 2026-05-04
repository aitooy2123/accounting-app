{{-- resources/views/purchases/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    {{-- HEADER WITH GRADIENT --}}
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl">
                            <i class="fas fa-edit text-2xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold font-kanit">แก้ไขเอกสารซื้อ</h1>
                    </div>
                    <p class="text-purple-100 font-kanit mt-1 flex items-center gap-2">
                        <i class="fas fa-hashtag text-sm"></i>
                        เลขที่เอกสาร: <span class="font-mono font-bold text-white">{{ $purchase->doc_no }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium backdrop-blur-sm
                        @if($purchase->status == 'ชำระแล้ว') bg-green-500/30 text-green-100
                        @elseif($purchase->status == 'ยกเลิก') bg-gray-500/30 text-gray-100
                        @else bg-yellow-500/30 text-yellow-100 @endif">
                        <i class="fas
                            @if($purchase->status == 'ชำระแล้ว') fa-check-circle
                            @elseif($purchase->status == 'ยกเลิก') fa-ban
                            @else fa-clock @endif mr-2"></i>
                        {{ $purchase->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        {{-- Hidden fields --}}
        <input type="hidden" name="vat" id="vat_amount" value="{{ old('vat', $purchase->vat) }}">
        <input type="hidden" name="total" id="total_amount" value="{{ old('total', $purchase->total) }}">

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
            {{-- Progress Steps --}}
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center text-purple-600">
                            <div class="bg-purple-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</div>
                            <span class="ml-2 text-sm font-medium text-purple-600">ข้อมูลเอกสาร</span>
                        </div>
                        <div class="w-12 h-0.5 bg-gray-300"></div>
                        <div class="flex items-center text-gray-400">
                            <div class="bg-gray-300 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                            <span class="ml-2 text-sm font-medium">ข้อมูลการเงิน</span>
                        </div>
                        <div class="w-12 h-0.5 bg-gray-300"></div>
                        <div class="flex items-center text-gray-400">
                            <div class="bg-gray-300 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                            <span class="ml-2 text-sm font-medium">รายละเอียด</span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="p-6 space-y-8">
                {{-- SECTION: ข้อมูลเอกสาร --}}
                <div class="group">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 font-kanit flex items-center gap-2 border-l-4 border-purple-500 pl-3">
                        <i class="fas fa-file-invoice text-purple-500"></i>
                        ข้อมูลเอกสาร
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="transform transition-all duration-200 hover:translate-x-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-hashtag text-gray-400 mr-1"></i>
                                เลขที่เอกสาร <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="doc_no" value="{{ old('doc_no', $purchase->doc_no) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-mono font-bold text-purple-600 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all @error('doc_no') border-red-300 @enderror"
                                   required>
                            @error('doc_no')<p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        <div class="transform transition-all duration-200 hover:translate-x-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                วันที่เอกสาร <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="doc_date" value="{{ old('doc_date', $purchase->doc_date instanceof \Carbon\Carbon ? $purchase->doc_date->format('Y-m-d') : $purchase->doc_date) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all @error('doc_date') border-red-300 @enderror">
                            @error('doc_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="transform transition-all duration-200 hover:translate-x-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-store text-gray-400 mr-1"></i>
                                เจ้าหนี้/ผู้ขาย <span class="text-red-500">*</span>
                            </label>
                            <select name="supplier_id" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-kanit focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all appearance-none bg-white @error('supplier_id') border-red-300 @enderror">
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

                        <div class="transform transition-all duration-200 hover:translate-x-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-building text-gray-400 mr-1"></i>
                                สาขา <span class="text-red-500">*</span>
                            </label>
                            <select name="branch_id" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-kanit focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all appearance-none bg-white @error('branch_id') border-red-300 @enderror">
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

                        <div class="transform transition-all duration-200 hover:translate-x-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-hourglass-half text-gray-400 mr-1"></i>
                                วันที่ครบกำหนด <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="due_date" value="{{ old('due_date', $purchase->due_date instanceof \Carbon\Carbon ? $purchase->due_date->format('Y-m-d') : $purchase->due_date) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all @error('due_date') border-red-300 @enderror">
                            @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="transform transition-all duration-200 hover:translate-x-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-tag text-gray-400 mr-1"></i>
                                สถานะ
                            </label>
                            <select name="status" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-kanit focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all appearance-none bg-white @error('status') border-red-300 @enderror">
                                <option value="ค้างชำระ" {{ old('status', $purchase->status) == 'ค้างชำระ' ? 'selected' : '' }}>🔴 ค้างชำระ</option>
                                <option value="ชำระแล้ว" {{ old('status', $purchase->status) == 'ชำระแล้ว' ? 'selected' : '' }}>🟢 ชำระแล้ว</option>
                                <option value="ยกเลิก" {{ old('status', $purchase->status) == 'ยกเลิก' ? 'selected' : '' }}>⚫ ยกเลิก</option>
                            </select>
                            @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Separator --}}
                <div class="border-t border-gray-100"></div>

                {{-- SECTION: ข้อมูลการเงิน --}}
                <div class="group">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 font-kanit flex items-center gap-2 border-l-4 border-purple-500 pl-3">
                        <i class="fas fa-calculator text-purple-500"></i>
                        ข้อมูลการเงิน
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="transform transition-all duration-200 hover:scale-105">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-money-bill-wave text-gray-400 mr-1"></i>
                                ยอดเงินก่อนภาษี (฿) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">฿</span>
                                <input type="text" name="subtotal_display" id="subtotal_display"
                                       value="{{ number_format(old('subtotal', $purchase->subtotal), 2) }}"
                                       class="currency-input w-full pl-8 pr-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all @error('subtotal') border-red-300 @enderror"
                                       autocomplete="off">
                            </div>
                            <input type="hidden" name="subtotal" id="subtotal" value="{{ old('subtotal', $purchase->subtotal) }}">
                            @error('subtotal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="transform transition-all duration-200 hover:scale-105">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-percent text-gray-400 mr-1"></i>
                                อัตราภาษี (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="vat_rate" id="vat_rate" step="0.01" min="0" max="100"
                                   value="{{ old('vat_rate', $purchase->vat_rate ?: 7) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all @error('vat_rate') border-red-300 @enderror">
                            @error('vat_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="transform transition-all duration-200 hover:scale-105">
                            <label class="block text-sm font-bold text-gray-700 mb-2 font-kanit">
                                <i class="fas fa-file-invoice-dollar text-gray-400 mr-1"></i>
                                ยอดรวมสุทธิ (฿)
                            </label>
                            <div class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl bg-gradient-to-r from-purple-50 to-indigo-50 text-sm font-bold text-purple-700 font-mono shadow-inner">
                                <span id="totalDisplay">฿ {{ number_format($purchase->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Summary Cards --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-200 p-2 rounded-lg">
                                        <i class="fas fa-receipt text-gray-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-kanit">ภาษีมูลค่าเพิ่ม</p>
                                        <p class="text-xl font-bold text-gray-800 font-mono" id="vatDisplay">฿ {{ number_format($purchase->vat, 2) }}</p>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <i class="fas fa-chart-line"></i> {{ $purchase->vat_rate ?? 7 }}%
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg">
                                        <i class="fas fa-coins text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-purple-100 font-kanit">รวมทั้งสิ้น</p>
                                        <p class="text-2xl font-bold text-white font-mono" id="grandTotalDisplay">฿ {{ number_format($purchase->total, 2) }}</p>
                                    </div>
                                </div>
                                <div class="text-purple-200 text-xs">
                                    <i class="fas fa-check-circle"></i> รวม VAT แล้ว
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Separator --}}
                <div class="border-t border-gray-100"></div>

                {{-- SECTION: หมายเหตุ --}}
                <div class="group">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 font-kanit flex items-center gap-2 border-l-4 border-purple-500 pl-3">
                        <i class="fas fa-sticky-note text-purple-500"></i>
รายละเอียดเเพิ่มเติม

                    </h2>
                    <div class="transform transition-all duration-200 hover:shadow-md">
                        <textarea name="note" rows="4"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-kanit focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all @error('note') border-red-300 @enderror"
                                  placeholder="📝 ระบุหมายเหตุเพิ่มเติม (ถ้ามี)">{{ old('note', $purchase->note) }}</textarea>
                        @error('note')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Meta Info --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex flex-wrap justify-between gap-3 text-xs text-gray-500">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-plus text-purple-400"></i>
                            <span>สร้างเมื่อ: {{ $purchase->created_at ? $purchase->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-edit text-purple-400"></i>
                            <span>แก้ไขล่าสุด: {{ $purchase->updated_at ? $purchase->updated_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        @if($purchase->created_by)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-check text-purple-400"></i>
                            <span>สร้างโดย: {{ $purchase->creator->name ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-5 flex justify-between items-center">
                <div class="flex gap-3">
                    <a href="{{ route('purchases.index') }}"
                       class="px-6 py-2.5 bg-white text-gray-700 rounded-xl text-sm font-bold font-kanit border-2 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        กลับ
                    </a>
                    <button type="button" onclick="confirmDelete()"
                            class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold font-kanit transition-all duration-200 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                        <i class="fas fa-trash-alt"></i>
                        ลบเอกสาร
                    </button>
                </div>
                <button type="submit" id="submitBtn"
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold font-kanit transition-all duration-200 shadow-lg hover:shadow-xl inline-flex items-center gap-2 transform hover:scale-105">
                    <i class="fas fa-save"></i>
                    อัปเดตเอกสารซื้อ
                </button>
            </div>
        </div>
    </form>

    {{-- Hidden Delete Form --}}
    <form id="deleteForm" action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ฟังก์ชัน format ตัวเลขเป็นสกุลเงินไทย
    function formatThaiCurrency(value) {
        if (isNaN(value) || value === '') return '0.00';
        return new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(parseFloat(value));
    }

    // ฟังก์ชันแปลง string สกุลเงินกลับเป็น number
    function parseCurrency(value) {
        if (!value) return 0;
        if (typeof value === 'string') {
            const cleaned = value.replace(/[^0-9.-]/g, '');
            const number = parseFloat(cleaned);
            return isNaN(number) ? 0 : number;
        }
        return isNaN(value) ? 0 : value;
    }

    // ฟังก์ชันคำนวณยอดรวม
    function calculateTotal() {
        const subtotalDisplay = document.getElementById('subtotal_display');
        let subtotal = subtotalDisplay ? parseCurrency(subtotalDisplay.value) : 0;

        if (subtotal === 0) {
            const subtotalHidden = document.getElementById('subtotal');
            if (subtotalHidden && subtotalHidden.value) {
                subtotal = parseFloat(subtotalHidden.value) || 0;
            }
        }

        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;
        const vat = subtotal * (vatRate / 100);
        const total = subtotal + vat;

        const subtotalHidden = document.getElementById('subtotal');
        if (subtotalHidden) subtotalHidden.value = subtotal.toFixed(2);

        const vatInput = document.getElementById('vat_amount');
        const totalInput = document.getElementById('total_amount');

        if (vatInput) vatInput.value = vat.toFixed(2);
        if (totalInput) totalInput.value = total.toFixed(2);

        const vatDisplay = document.getElementById('vatDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');

        if (vatDisplay) vatDisplay.innerHTML = '฿ ' + formatThaiCurrency(vat);
        if (totalDisplay) totalDisplay.innerHTML = '฿ ' + formatThaiCurrency(total);
        if (grandTotalDisplay) grandTotalDisplay.innerHTML = '฿ ' + formatThaiCurrency(total);
    }

    // ฟังก์ชันยืนยันการลบ
    window.confirmDelete = function() {
        const docNoElement = document.querySelector('input[name="doc_no"]');
        const docNo = docNoElement ? escapeHtml(docNoElement.value) : '';

        Swal.fire({
            title: '<span class="font-kanit">ยืนยันการลบ?</span>',
            html: `
                <div class="font-kanit">
                    <p class="mb-2">คุณต้องการลบเอกสาร</p>
                    <p class="font-bold text-purple-600 text-lg">${docNo}</p>
                    <p class="text-red-500 text-sm mt-3">⚠️ การลบไม่สามารถกู้คืนได้</p>
                    <p class="text-yellow-600 text-xs mt-2">⚠️ ข้อมูลที่เกี่ยวข้องจะถูกลบทั้งหมด</p>
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
    };

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function validateForm() {
        const docDateValue = document.querySelector('input[name="doc_date"]')?.value;
        const dueDateValue = document.querySelector('input[name="due_date"]')?.value;

        if (!docDateValue || !dueDateValue) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'กรุณากรอกวันที่ให้ครบถ้วน',
                confirmButtonColor: '#7C3AED',
                customClass: { popup: 'rounded-2xl font-kanit' }
            });
            return false;
        }

        const docDate = new Date(docDateValue);
        const dueDate = new Date(dueDateValue);
        const supplierSelect = document.querySelector('select[name="supplier_id"]');
        const branchSelect = document.querySelector('select[name="branch_id"]');

        const subtotalDisplay = document.getElementById('subtotal_display');
        let subtotal = subtotalDisplay ? parseCurrency(subtotalDisplay.value) : 0;

        if (subtotal === 0) {
            const subtotalHidden = document.getElementById('subtotal');
            if (subtotalHidden && subtotalHidden.value) {
                subtotal = parseFloat(subtotalHidden.value) || 0;
            }
        }

        if (isNaN(docDate.getTime()) || isNaN(dueDate.getTime())) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'กรุณากรอกวันที่ให้ถูกต้อง',
                confirmButtonColor: '#7C3AED',
                customClass: { popup: 'rounded-2xl font-kanit' }
            });
            return false;
        }

        if (!supplierSelect?.value) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'กรุณาเลือกผู้ขาย',
                confirmButtonColor: '#7C3AED',
                customClass: { popup: 'rounded-2xl font-kanit' }
            });
            return false;
        }

        if (!branchSelect?.value) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'กรุณาเลือกสาขา',
                confirmButtonColor: '#7C3AED',
                customClass: { popup: 'rounded-2xl font-kanit' }
            });
            return false;
        }

        dueDate.setHours(0, 0, 0, 0);
        docDate.setHours(0, 0, 0, 0);

        if (dueDate < docDate) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'วันที่ครบกำหนดต้องไม่ก่อนวันที่เอกสาร',
                confirmButtonColor: '#7C3AED',
                customClass: { popup: 'rounded-2xl font-kanit' }
            });
            return false;
        }

        if (subtotal <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'ยอดเงินก่อนภาษีต้องมากกว่า 0',
                confirmButtonColor: '#7C3AED',
                customClass: { popup: 'rounded-2xl font-kanit' }
            });
            return false;
        }

        const vatRate = parseFloat(document.getElementById('vat_rate')?.value) || 0;
        if (vatRate < 0 || vatRate > 100) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'อัตราภาษีต้องอยู่ระหว่าง 0-100%',
                confirmButtonColor: '#7C3AED',
                customClass: { popup: 'rounded-2xl font-kanit' }
            });
            return false;
        }

        return true;
    }

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
            customClass: { popup: 'rounded-2xl font-kanit' }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();

        const subtotalDisplay = document.getElementById('subtotal_display');
        if (subtotalDisplay) {
            subtotalDisplay.addEventListener('input', function(e) {
                let value = this.value.replace(/[^0-9.]/g, '');
                const parts = value.split('.');
                if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
                if (parts.length === 2 && parts[1].length > 2) {
                    value = parts[0] + '.' + parts[1].substring(0, 2);
                }
                this.value = value;
                calculateTotal();
            });

            subtotalDisplay.addEventListener('blur', function() {
                let value = parseCurrency(this.value);
                if (value === 0 && this.value !== '') {
                    this.value = '';
                } else if (value > 0) {
                    this.value = formatThaiCurrency(value);
                }
                calculateTotal();
            });

            subtotalDisplay.addEventListener('focus', function() {
                this.select();
            });
        }

        const vatRateField = document.getElementById('vat_rate');
        if (vatRateField) {
            vatRateField.addEventListener('input', calculateTotal);
            vatRateField.addEventListener('blur', function() {
                if (this.value) {
                    let value = parseFloat(this.value);
                    if (value < 0) this.value = 0;
                    if (value > 100) this.value = 100;
                    this.value = value.toFixed(2);
                    calculateTotal();
                } else {
                    this.value = '7.00';
                    calculateTotal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                if (validateForm()) {
                    document.getElementById('purchaseForm').dispatchEvent(new Event('submit'));
                }
            }
        });
    });

    const form = document.getElementById('purchaseForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            const subtotalDisplay = document.getElementById('subtotal_display');
            const subtotalHidden = document.getElementById('subtotal');
            if (subtotalDisplay && subtotalHidden) {
                subtotalHidden.value = parseCurrency(subtotalDisplay.value).toFixed(2);
            }

            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse mr-2"></i>กำลังบันทึก...';
            }

            return true;
        });
    }

    let formChanged = false;
    const formFields = document.getElementById('purchaseForm')?.querySelectorAll('input, select, textarea');

    if (formFields) {
        formFields.forEach(field => {
            if (field.type !== 'hidden') {
                field.addEventListener('change', () => { formChanged = true; });
                field.addEventListener('keyup', () => { formChanged = true; });
            }
        });
    }

    let beforeUnloadHandler = function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'คุณยังไม่ได้บันทึกการเปลี่ยนแปลง ต้องการออกจากหน้านี้หรือไม่?';
            return e.returnValue;
        }
    };

    window.addEventListener('beforeunload', beforeUnloadHandler);

    if (form) {
        form.addEventListener('submit', function() {
            formChanged = false;
            window.removeEventListener('beforeunload', beforeUnloadHandler);
        });
    }

    @if($errors->any())
        const serverErrors = @json($errors->messages());
        showValidationErrors(serverErrors);
    @endif
</script>
@endpush

@push('styles')
<style>
    /* Modern UI Styles */
    .font-kanit {
        font-family: 'Kanit', sans-serif;
    }

    /* Smooth transitions */
    * {
        transition: all 0.2s ease;
    }

    /* Card hover effects */
    .group:hover .transform {
        transform: translateX(2px);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #8B5CF6, #6366F1);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #7C3AED, #4F46E5);
    }

    /* Currency input styling */
    .currency-input {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }

    .currency-input:focus {
        text-align: left;
    }

    /* Input focus effects */
    input:focus, select:focus, textarea:focus {
        outline: none;
    }

    /* Button hover effects */
    button {
        position: relative;
        overflow: hidden;
    }

    button::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    button:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Number input spinners */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 0.5;
    }

    input[type="number"]:hover::-webkit-inner-spin-button,
    input[type="number"]:hover::-webkit-outer-spin-button {
        opacity: 1;
    }

    /* Animation for loading */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .bg-white.rounded-2xl {
        animation: fadeInUp 0.4s ease-out;
    }

    /* Custom select styling */
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    /* Disabled button style */
    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Loading pulse animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .loading-pulse {
        animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush

@endsection
