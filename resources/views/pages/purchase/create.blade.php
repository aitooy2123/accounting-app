@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-indigo-600 text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800">
                                สร้างเอกสารซื้อใหม่
                            </h1>
                            <p class="text-sm text-gray-500 mt-0.5">กรอกข้อมูลเอกสารซื้อให้ครบถ้วน</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="bg-gray-100 rounded-lg px-3 py-1.5 flex items-center gap-2">
                        <i class="fas fa-asterisk text-gray-500 text-xs"></i>
                        <span class="text-xs text-gray-600">ฟิลด์ที่มี <span class="text-red-500 font-medium">*</span> จำเป็นต้องกรอก</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN FORM CARD --}}
        <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" novalidate>
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">

                {{-- SECTION: ข้อมูลเอกสาร --}}
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-7 h-7 bg-gray-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-file-alt text-gray-500 text-sm"></i>
                        </div>
                        <h2 class="text-base font-medium text-gray-800">ข้อมูลเอกสาร</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- เลขที่เอกสาร --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                เลขที่เอกสาร <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="doc_no" value="{{ old('doc_no', $autoDocNo) }}" readonly
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-600 font-mono">
                            @error('doc_no')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- วันที่เอกสาร --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                วันที่เอกสาร <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="doc_date" value="{{ old('doc_date', now()->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors @error('doc_date') border-red-300 bg-red-50 @enderror">
                            @error('doc_date')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- เจ้าหนี้/ผู้ขาย --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                เจ้าหนี้/ผู้ขาย <span class="text-red-500">*</span>
                            </label>
                            <select name="supplier_id" id="supplier_id" required
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors @error('supplier_id') border-red-300 bg-red-50 @enderror">
                                <option value="">-- เลือกเจ้าหนี้ --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                            data-code="{{ $supplier->code }}"
                                            data-name="{{ $supplier->name }}"
                                            data-tax="{{ $supplier->tax_id ?? '' }}"
                                            data-phone="{{ $supplier->phone ?? '' }}"
                                            data-email="{{ $supplier->email ?? '' }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->code }} - {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror

                            {{-- Supplier Info Card --}}
                            <div id="supplierInfo" class="mt-3 hidden">
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-indigo-50 rounded-md flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-store text-indigo-500 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-800 text-sm" id="supplierName"></h3>
                                            <p class="text-xs text-gray-400 mt-0.5" id="supplierCode"></p>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5">
                                                <span class="text-xs text-gray-500" id="supplierTax">
                                                    <i class="fas fa-id-card mr-1 w-3"></i> <span></span>
                                                </span>
                                                <span class="text-xs text-gray-500" id="supplierPhone">
                                                    <i class="fas fa-phone mr-1 w-3"></i> <span></span>
                                                </span>
                                                <span class="text-xs text-gray-500" id="supplierEmail">
                                                    <i class="fas fa-envelope mr-1 w-3"></i> <span></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- สาขา --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                สาขา <span class="text-red-500">*</span>
                            </label>
                            <select name="branch_id" id="branch_id" required
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors @error('branch_id') border-red-300 bg-red-50 @enderror">
                                <option value="">-- เลือกสาขา --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                            data-code="{{ $branch->code }}"
                                            data-name="{{ $branch->name }}"
                                            data-address="{{ $branch->address ?? '' }}"
                                            data-phone="{{ $branch->phone ?? '' }}"
                                            {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->code }} - {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror

                            {{-- Branch Info Card --}}
                            <div id="branchInfo" class="mt-3 hidden">
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-emerald-50 rounded-md flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-building text-emerald-500 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-800 text-sm" id="branchName"></h3>
                                            <p class="text-xs text-gray-400 mt-0.5" id="branchCode"></p>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5">
                                                <span class="text-xs text-gray-500" id="branchAddress">
                                                    <i class="fas fa-map-marker-alt mr-1 w-3"></i> <span></span>
                                                </span>
                                                <span class="text-xs text-gray-500" id="branchPhone">
                                                    <i class="fas fa-phone mr-1 w-3"></i> <span></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- วันที่ครบกำหนด --}}
                        <div class="md:col-span-2 md:max-w-xs">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                วันที่ครบกำหนด <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors @error('due_date') border-red-300 bg-red-50 @enderror">
                            @error('due_date')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION: ข้อมูลการเงิน --}}
                <div class="p-6 border-b border-gray-100 bg-gray-50/40">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-7 h-7 bg-gray-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-coins text-gray-500 text-sm"></i>
                        </div>
                        <h2 class="text-base font-medium text-gray-800">ข้อมูลการเงิน</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                ยอดเงินก่อนภาษี <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400">฿</span>
                                </div>
                                <input type="number" name="subtotal" id="subtotal" step="0.01" min="0"
                                       value="{{ old('subtotal', 0) }}" required
                                       class="w-full pl-8 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm text-right focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                อัตราภาษี (%)
                            </label>
                            <div class="relative">
                                <input type="number" name="vat_rate" id="vat_rate" step="0.01" min="0" max="100"
                                       value="{{ old('vat_rate', 7) }}" required
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-right focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                ยอดรวมสุทธิ
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400">฿</span>
                                </div>
                                <div id="totalDisplay" class="w-full pl-8 pr-3 py-2.5 border border-gray-200 rounded-lg bg-gray-100 text-sm font-medium text-gray-700 text-right">
                                    0.00
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-white rounded-lg p-3 flex justify-between items-center border border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                                    <i class="fas fa-percent text-gray-500 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">ภาษีมูลค่าเพิ่ม</p>
                                    <p class="text-sm font-medium text-gray-700">VAT <span id="vatRateDisplay">7</span>%</p>
                                </div>
                            </div>
                            <span id="vatDisplay" class="font-medium text-gray-800">฿ 0.00</span>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-3 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                                    <i class="fas fa-receipt text-indigo-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-indigo-500">รวมทั้งสิ้น</p>
                                    <p class="text-sm font-medium text-indigo-700">Grand Total</p>
                                </div>
                            </div>
                            <span id="grandTotalDisplay" class="font-semibold text-indigo-700 text-lg">฿ 0.00</span>
                        </div>
                    </div>
                </div>

                {{-- SECTION: หมายเหตุ --}}
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-7 h-7 bg-gray-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-pen-alt text-gray-500 text-sm"></i>
                        </div>
                        <h2 class="text-base font-medium text-gray-800">รายละเอียดเเพิ่มเติม</h2>
                    </div>

                    <div class="relative">
                        <textarea name="note" rows="3"
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors resize-none"
                                  placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี) เช่น เงื่อนไขการชำระเงิน, ส่วนลดพิเศษ, หมายเหตุการจัดส่ง...">{{ old('note') }}</textarea>
                        <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                            <span id="noteCount">0</span>/500
                        </div>
                    </div>
                </div>
            </div>

            {{-- BUTTONS --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('purchases.index') }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 order-2 sm:order-1">
                    <i class="fas fa-times"></i>
                    <span>ยกเลิก</span>
                </a>
                <button type="submit" id="submitBtn"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center justify-center gap-2 order-1 sm:order-2">
                    <i class="fas fa-save"></i>
                    <span>บันทึกเอกสารซื้อ</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // แสดงข้อมูล Supplier เมื่อเลือก
    const supplierSelect = document.getElementById('supplier_id');
    const supplierInfo = document.getElementById('supplierInfo');

    supplierSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];

        if (this.value) {
            document.getElementById('supplierName').textContent = selected.getAttribute('data-name');
            document.getElementById('supplierCode').innerHTML = `<i class="fas fa-code"></i> ${selected.getAttribute('data-code')}`;
            document.getElementById('supplierTax').querySelector('span').textContent = selected.getAttribute('data-tax') || '-';
            document.getElementById('supplierPhone').querySelector('span').textContent = selected.getAttribute('data-phone') || '-';
            document.getElementById('supplierEmail').querySelector('span').textContent = selected.getAttribute('data-email') || '-';
            supplierInfo.classList.remove('hidden');
        } else {
            supplierInfo.classList.add('hidden');
        }
    });

    // แสดงข้อมูล Branch เมื่อเลือก
    const branchSelect = document.getElementById('branch_id');
    const branchInfo = document.getElementById('branchInfo');

    branchSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];

        if (this.value) {
            document.getElementById('branchName').textContent = selected.getAttribute('data-name');
            document.getElementById('branchCode').innerHTML = `<i class="fas fa-code"></i> ${selected.getAttribute('data-code')}`;
            document.getElementById('branchAddress').querySelector('span').textContent = selected.getAttribute('data-address') || '-';
            document.getElementById('branchPhone').querySelector('span').textContent = selected.getAttribute('data-phone') || '-';
            branchInfo.classList.remove('hidden');
        } else {
            branchInfo.classList.add('hidden');
        }
    });

    // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        if (supplierSelect.value) supplierSelect.dispatchEvent(new Event('change'));
        if (branchSelect.value) branchSelect.dispatchEvent(new Event('change'));
        calculateTotal();

        const noteTextarea = document.querySelector('textarea[name="note"]');
        if (noteTextarea) {
            noteTextarea.addEventListener('input', updateNoteCount);
            updateNoteCount();
        }

        document.getElementById('subtotal').addEventListener('input', calculateTotal);
        document.getElementById('vat_rate').addEventListener('input', function() {
            calculateTotal();
            document.getElementById('vatRateDisplay').textContent = this.value || 0;
        });
    });

    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;
        const vat = subtotal * (vatRate / 100);
        const total = subtotal + vat;

        const formatCurrency = (value) => '฿ ' + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const formatNumber = (value) => value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        document.getElementById('vatDisplay').innerHTML = formatCurrency(vat);
        document.getElementById('totalDisplay').innerHTML = formatNumber(total);
        document.getElementById('grandTotalDisplay').innerHTML = formatCurrency(total);
        document.getElementById('vatRateDisplay').textContent = vatRate;
    }

    function updateNoteCount() {
        const note = document.querySelector('textarea[name="note"]');
        const countSpan = document.getElementById('noteCount');
        if (note && countSpan) {
            countSpan.textContent = note.value.length;
        }
    }

    function validateForm() {
        const supplier = supplierSelect.value;
        const branch = branchSelect.value;
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;

        if (!supplier) {
            alert('กรุณาเลือกเจ้าหนี้/ผู้ขาย');
            supplierSelect.focus();
            return false;
        }
        if (!branch) {
            alert('กรุณาเลือกสาขา');
            branchSelect.focus();
            return false;
        }
        if (subtotal <= 0) {
            alert('กรุณาระบุยอดเงินก่อนภาษีมากกว่า 0');
            document.getElementById('subtotal').focus();
            return false;
        }
        return true;
    }

    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn.disabled) return false;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>กำลังบันทึก...</span>';
        return true;
    });

    // Ctrl+S shortcut
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (validateForm()) {
                document.getElementById('purchaseForm').dispatchEvent(new Event('submit'));
            }
        }
    });
</script>
@endpush
@endsection
