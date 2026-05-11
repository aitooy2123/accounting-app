{{-- resources/views/reports/journal.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">

    {{-- หัวข้อหน้า + Breadcrumb --}}
    <div class="mb-8">
        <nav class="flex items-center text-sm text-gray-500 mb-2 font-kanit">
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-600">หน้าแรก</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-emerald-600 font-medium">สมุดรายวันทั่วไป</span>
        </nav>
        {{-- <div class="flex justify-between items-start flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 font-kanit tracking-tight">สมุดรายวันทั่วไป</h1>
                <p class="text-gray-500 mt-1">บันทึกบัญชีแยกประเภททั่วไป (General Journal) แบบ Double-Entry</p>
            </div>
            <div class="flex gap-2 print:hidden">
                <button type="button" id="exportExcelBtn" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print"></i> พิมพ์
                </button>
            </div>
        </div> --}}
    </div>

    {{-- ฟอร์มเลือกช่วงวันที่และลูกค้า --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 print:hidden border border-gray-100">
        <form method="GET" action="{{ route('reports.journal') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-kanit text-gray-600 mb-1">
                        <i class="far fa-calendar-alt mr-1 text-emerald-500"></i> วันที่เริ่มต้น
                    </label>
                    <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-kanit text-gray-600 mb-1">
                        <i class="far fa-calendar-check mr-1 text-emerald-500"></i> วันที่สิ้นสุด
                    </label>
                    <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-kanit text-gray-600 mb-1">
                        <i class="fas fa-user mr-1 text-emerald-500"></i> เลือกลูกค้า
                    </label>
                    <select name="customer_id" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="">👥 ทั้งหมด</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-kanit text-gray-600 mb-1">
                        <i class="fas fa-file-invoice mr-1 text-emerald-500"></i> ประเภทเอกสาร
                    </label>
                    <select name="document_type" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="">📄 ทั้งหมด</option>
                        <option value="quotation" {{ request('document_type') == 'quotation' ? 'selected' : '' }}>📊 ใบเสนอราคา</option>
                        <option value="invoice" {{ request('document_type') == 'invoice' ? 'selected' : '' }}>📃 ใบกำกับภาษี</option>
                        <option value="receipt" {{ request('document_type') == 'receipt' ? 'selected' : '' }}>💰 ใบรับเงิน</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all font-kanit shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i> แสดงผล
                    </button>
                    <button type="button" onclick="resetFilters()" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all">
                        <i class="fas fa-undo-alt"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- สรุปยอดแยกรายลูกค้า - แสดงเฉพาะเมื่อมีข้อมูล
    @if(isset($customerSummary) && $customerSummary->count() > 0)
    <div class="mb-8 print:hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800 font-kanit">
                <i class="fas fa-chart-pie text-emerald-500 mr-2"></i>สรุปยอดแยกรายลูกค้า
            </h2>
            <button type="button" onclick="toggleCustomerSummary()" class="text-sm text-emerald-600 hover:text-emerald-800">
                <i class="fas fa-chevron-up" id="summaryToggleIcon"></i>
            </button>
        </div>
        <div id="customerSummaryGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($customerSummary as $summary)
            <div class="group bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-5 border border-emerald-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-200 flex items-center justify-center">
                                <i class="fas fa-user text-emerald-700 text-sm"></i>
                            </div>
                            <p class="font-bold text-gray-800">{{ $summary->customer_name }}</p>
                        </div>
                        <p class="text-2xl font-black text-emerald-700 font-mono">
                            ฿ {{ number_format($summary->total_amount, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">เลขผู้เสียภาษี: {{ $summary->customer_tax_id ?: '-' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs bg-white/80 px-2.5 py-1 rounded-full text-gray-600 shadow-sm">
                            <i class="fas fa-file-alt mr-1"></i> {{ $summary->transaction_count }} รายการ
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-emerald-200 flex justify-between items-center">
                    <span class="text-xs text-gray-500">
                        <i class="far fa-calendar-alt mr-1"></i> {{ request('start_date', now()->startOfMonth()->format('d/m/Y')) }} - {{ request('end_date', now()->format('d/m/Y')) }}
                    </span>
                    <a href="{{ route('reports.customer-statement', $summary->customer_id) }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"
                       class="text-sm text-emerald-600 hover:text-emerald-800 flex items-center gap-1 font-medium">
                        ดูรายละเอียด <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif --}}

    {{-- ตารางสมุดรายวัน --}}
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full font-kanit text-sm" id="journalTable">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-4 text-center w-24">วันที่</th>
                        <th class="px-4 py-4 text-center w-28">เลขที่เอกสาร</th>
                        <th class="px-4 py-4 text-left">ลูกค้า</th>
                        <th class="px-4 py-4 text-left">รายการ</th>
                        <th class="px-4 py-4 text-right w-32">เดบิต (Dr.)</th>
                        <th class="px-4 py-4 text-right w-32">เครดิต (Cr.)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $index => $transaction)
                        {{-- บรรทัดแรก: ขายสินค้า (เดบิต) --}}
                        <tr class="hover:bg-gray-50 transition-colors {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/30' }}">
                            <td class="px-4 py-3 text-center font-mono text-gray-600 align-top" rowspan="{{ $transaction->rowspan }}">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                <div class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($transaction->date)->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 text-center font-mono font-medium align-top" rowspan="{{ $transaction->rowspan }}">
                                <span class="bg-gray-100 px-2 py-1 rounded-lg text-xs">{{ $transaction->doc_no }}</span>
                            </td>
                            <td class="px-4 py-3 align-top" rowspan="{{ $transaction->rowspan }}">
                                <div class="font-medium text-gray-800">{{ $transaction->customer_name }}</div>
                                <div class="text-xs text-gray-400">{{ $transaction->customer_tax_id }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    <span class="font-semibold text-gray-800">ลูกหนี้การค้า</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-700 font-bold align-top">
                                ฿ {{ number_format($transaction->total, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-gray-400">-</td>
                        </tr>
                        {{-- บรรทัดที่สอง: รายได้จากการขาย (เครดิต) --}}
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/30' }}">
                            <td class="px-4 py-2 pl-12">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-rose-400 opacity-60"></div>
                                    <span class="text-gray-700">รายได้จากการขาย</span>
                                    @if($transaction->description)
                                        <span class="text-gray-400 text-xs">({{ Str::limit($transaction->description, 50) }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right font-mono text-gray-400">-</td>
                            <td class="px-4 py-2 text-right font-mono text-rose-700 font-bold">
                                ฿ {{ number_format($transaction->subtotal, 2) }}
                            </td>
                        </tr>
                        {{-- บรรทัดที่สาม: VAT (ถ้ามี) --}}
                        @if($transaction->vat > 0)
                        <tr class="border-b border-gray-100 {{ $index % 2 == 0 ? 'bg-gray-50/30' : 'bg-gray-100/30' }}">
                            <td class="px-4 py-2 pl-12">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-amber-400 opacity-60"></div>
                                    <span class="text-gray-600">ภาษีขาย (VAT {{ $transaction->vat_rate }}%)</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right font-mono text-gray-400">-</td>
                            <td class="px-4 py-2 text-right font-mono text-amber-700 font-medium">
                                ฿ {{ number_format($transaction->vat, 2) }}
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-400">
                                <i class="fas fa-book-open text-6xl mb-4 block opacity-20"></i>
                                <p class="text-lg font-medium">ไม่มีรายการในช่วงวันที่นี้</p>
                                <p class="text-sm mt-1">ลองเปลี่ยนช่วงวันที่หรือเลือกลูกค้าอื่น</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($transactions->count() > 0)
                <tfoot class="bg-gray-100 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-right font-bold text-gray-800">
                            <i class="fas fa-calculator mr-2 text-emerald-600"></i> รวมทั้งสิ้น:
                        </td>
                        <td class="px-4 py-4 text-right font-mono font-bold text-emerald-800 text-lg">
                            ฿ {{ number_format($totals['total_debit'], 2) }}
                        </td>
                        <td class="px-4 py-4 text-right font-mono font-bold text-rose-800 text-lg">
                            ฿ {{ number_format($totals['total_credit'], 2) }}
                        </td>
                    </tr>
                    <tr class="bg-emerald-50">
                        <td colspan="6" class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-4 text-sm">
                                <span class="text-gray-600">
                                    <i class="fas fa-balance-scale mr-1 text-emerald-600"></i>
                                    ผลต่าง (Debit - Credit):
                                </span>
                                <span class="font-bold {{ ($totals['total_debit'] - $totals['total_credit']) == 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ number_format($totals['total_debit'] - $totals['total_credit'], 2) }}
                                </span>
                                @if(($totals['total_debit'] - $totals['total_credit']) == 0)
                                    <span class="text-emerald-600 text-xs"><i class="fas fa-check-circle"></i> สมดุล</span>
                                @else
                                    <span class="text-red-600 text-xs"><i class="fas fa-exclamation-triangle"></i> ไม่สมดุล</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Footer Info --}}
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400 flex justify-between items-center print:hidden">
            <div>
                <i class="far fa-clock mr-1"></i> สร้างเมื่อ {{ now()->format('d/m/Y H:i:s') }}
            </div>
            <div>
                <i class="fas fa-database mr-1"></i> พบรายการทั้งหมด {{ $transactions->count() }} รายการ
            </div>
        </div>
    </div>
</div>

<script>
// Reset Filters Function
function resetFilters() {
    // Reset select elements
    const customerSelect = document.querySelector('select[name="customer_id"]');
    const docTypeSelect = document.querySelector('select[name="document_type"]');

    if (customerSelect) customerSelect.value = '';
    if (docTypeSelect) docTypeSelect.value = '';

    // Reset date inputs
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');

    if (startDateInput) {
        startDateInput.value = '{{ now()->startOfMonth()->format("Y-m-d") }}';
    }
    if (endDateInput) {
        endDateInput.value = '{{ now()->format("Y-m-d") }}';
    }

    // Submit the form
    const form = document.getElementById('filterForm');
    if (form) form.submit();
}

// Toggle Customer Summary
function toggleCustomerSummary() {
    const grid = document.getElementById('customerSummaryGrid');
    const icon = document.getElementById('summaryToggleIcon');

    if (grid && icon) {
        if (grid.style.display === 'none') {
            grid.style.display = 'grid';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            grid.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }
}

// Export to Excel
document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
    const form = document.getElementById('filterForm');
    if (form) {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        window.location.href = '{{ route("reports.journal.export") }}?' + params.toString();
    }
});

// Keyboard shortcut (Ctrl+P for print)
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
});

// Auto-hide success messages after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});
</script>

<style>
@media print {
    body {
        background: white !important;
        margin: 0;
        padding: 0;
    }
    .print\:hidden {
        display: none !important;
    }
    .shadow-xl, .shadow-lg, .shadow-sm {
        box-shadow: none !important;
    }
    .bg-gradient-to-r {
        background: #1f2937 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .bg-gray-100, .bg-gray-50, .bg-emerald-50, .bg-blue-100, .bg-purple-100, .bg-amber-100 {
        background-color: #f3f4f6 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .rounded-2xl, .rounded-xl {
        border-radius: 0 !important;
    }
    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    table {
        page-break-inside: avoid;
    }
    thead {
        display: table-header-group;
    }
    tfoot {
        display: table-footer-group;
    }
    a {
        text-decoration: none !important;
        color: black !important;
    }
}
</style>
@endsection
