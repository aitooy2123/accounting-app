@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 font-kanit">แก้ไขเอกสารซื้อ</h1>
        <p class="text-sm text-gray-500 font-kanit mt-1">เลขที่เอกสาร: <span class="font-mono font-bold text-purple-600">{{ $purchase->doc_no }}</span></p>
    </div>

    {{-- FORM --}}
    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            {{-- SECTION: ข้อมูลเอกสาร --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-purple-600 uppercase tracking-wider mb-4 font-kanit">
                    <i class="fas fa-file-invoice mr-2"></i>ข้อมูลเอกสาร
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">เลขที่เอกสาร</label>
                        <input type="text" name="doc_no" value="{{ old('doc_no', $purchase->doc_no) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-mono font-bold text-purple-600 @error('doc_no') border-red-300 @enderror">
                        @error('doc_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">วันที่เอกสาร</label>
                        <input type="date" name="doc_date" value="{{ old('doc_date', $purchase->doc_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('doc_date') border-red-300 @enderror">
                        @error('doc_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">เจ้าหนี้/ผู้ขาย</label>
                        <select name="supplier_id" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit @error('supplier_id') border-red-300 @enderror">
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->code }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">สาขา</label>
                        <select name="branch_id" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit @error('branch_id') border-red-300 @enderror">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $purchase->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->code }} - {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">วันที่ครบกำหนด</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $purchase->due_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('due_date') border-red-300 @enderror">
                        @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">สถานะ</label>
                        <select name="status" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit @error('status') border-red-300 @enderror">
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
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">ยอดเงินก่อนภาษี (฿)</label>
                        <input type="number" name="subtotal" id="subtotal" step="0.01" min="0"
                               value="{{ old('subtotal', $purchase->subtotal) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('subtotal') border-red-300 @enderror"
                               onchange="calculateTotal()">
                        @error('subtotal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">อัตราภาษี (%)</label>
                        <input type="number" name="vat_rate" id="vat_rate" step="0.01" min="0" max="100"
                               value="{{ old('vat_rate', $purchase->vat_rate) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('vat_rate') border-red-300 @enderror"
                               onchange="calculateTotal()">
                        @error('vat_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1 font-kanit">ยอดรวมสุทธิ (฿)</label>
                        <div id="totalDisplay" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl bg-purple-50 text-sm font-bold text-purple-700 font-mono">
                            ฿ {{ number_format($purchase->total, 2) }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <span class="text-gray-500 font-kanit">ภาษีมูลค่าเพิ่ม:</span>
                        <span id="vatDisplay" class="font-bold text-gray-700 font-mono ml-2">฿ {{ number_format($purchase->vat, 2) }}</span>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-3">
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
                <textarea name="note" rows="3" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-kanit @error('note') border-red-300 @enderror">{{ old('note', $purchase->note) }}</textarea>
                @error('note')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- BUTTONS --}}
        <div class="flex justify-between">
            <button type="button" onclick="confirmDelete()"
                    class="px-6 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold font-kanit hover:bg-red-700 transition-all">
                <i class="fas fa-trash-alt mr-2"></i>ลบเอกสาร
            </button>

            <div class="flex space-x-3">
                <a href="{{ route('purchases.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold font-kanit hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-xl text-sm font-bold font-kanit transition-all shadow-lg shadow-purple-200/50">
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
    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;

        const vat = subtotal * (vatRate / 100);
        const total = subtotal + vat;

        document.getElementById('vatDisplay').textContent = '฿ ' + vat.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('totalDisplay').textContent = '฿ ' + total.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('grandTotalDisplay').textContent = '฿ ' + total.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function confirmDelete() {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            html: `คุณต้องการลบเอกสาร <strong>{{ $purchase->doc_no }}</strong> ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>ลบ',
            cancelButtonText: 'ยกเลิก',
            customClass: { popup: 'font-kanit rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    }

    document.getElementById('purchaseForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังบันทึก...';
    });

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });
</script>
@endpush
@endsection
