{{-- resources/views/sales/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">

    {{-- ACTION BUTTONS TOP --}}
<div class="mb-6 flex flex-wrap justify-between items-center gap-4 print:hidden">
    <!-- ปุ่มย้อนกลับ: ปรับให้ดูเป็นปุ่มเบาๆ (Ghost Button) -->
    <a href="{{ route('sales.index') }}"
       class="group flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-all duration-300 font-kanit">
        <div class="p-2 rounded-full group-hover:bg-blue-50 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </div>
        <span>กลับหน้ารายการ</span>
    </a>

    <div class="flex items-center gap-3">
        <!-- Dropdown Group -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false"
                class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:border-blue-400 hover:text-blue-600 font-kanit transition-all duration-200 flex items-center gap-2 shadow-sm active:scale-95">
                <i class="fas fa-file-invoice-dollar text-gray-400" :class="open ? 'text-blue-500' : ''"></i>
                <span>จัดการเอกสาร</span>
                <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <!-- Dropdown Menu: เพิ่ม Blur และปรับเงาให้ดู Soft ขึ้น -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                 class="absolute right-0 mt-3 w-60 bg-white/95 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-2xl z-[60] py-2 origin-top-right overflow-hidden">

                <div class="px-4 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">ตัวเลือกเอกสาร</div>

                <!-- Print -->
                <button onclick="window.print()" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 font-kanit flex items-center gap-3 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 group-hover:bg-white flex items-center justify-center transition-colors">
                        <i class="fas fa-print"></i>
                    </div>
                    พิมพ์เอกสาร
                </button>

                <!-- PDF/Quotation -->
                <a href="{{ route('quotations.showsale', $sale->id) }}" class="px-4 py-2.5 text-gray-700 hover:bg-red-50 hover:text-red-700 font-kanit flex items-center gap-3 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-red-50 group-hover:bg-white flex items-center justify-center transition-colors text-red-500">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    ใบเสนอราคา (PDF)
                </a>

                <!-- Excel Export -->
                <a href="{{ route('sales.export', $sale->id) }}" class="px-4 py-2.5 text-gray-700 hover:bg-green-50 hover:text-green-700 font-kanit flex items-center gap-3 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-green-50 group-hover:bg-white flex items-center justify-center transition-colors text-green-600">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    ส่งออก Excel
                </a>

               </div>
        </div>

        <!-- Main Action: ปรับสี Blue ให้ดูโมเดิร์นและเพิ่ม Hover Effect -->
        <a href="{{ route('sales.edit', $sale) }}"
           class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-kanit transition-all duration-300 flex items-center gap-2 shadow-lg shadow-blue-200 hover:shadow-blue-300 active:scale-95">
            <i class="fas fa-edit"></i>
            <span>แก้ไขข้อมูล</span>
        </a>
    </div>
</div>

    {{-- MAIN INVOICE CARD --}}
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden print:shadow-none print:border-0">

        {{-- TOP HEADER BAR --}}
        <div class="h-3 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

        <div class="p-8 md:p-12">
            {{-- BRANDING & DOC INFO --}}
            <div class="flex flex-col md:flex-row justify-between gap-8 mb-12">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 font-kanit tracking-tight mb-1">ใบกำกับภาษี / ใบแจ้งหนี้</h1>
                    <p class="text-gray-500 font-kanit">Tax Invoice / Receipt</p>

                    <div class="mt-8 space-y-1 text-sm text-gray-600 font-kanit">
                        <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">ข้อมูลลูกค้า</p>
                        <p class="text-lg text-blue-700 font-bold">{{ $sale->customer->name ?? 'ไม่ระบุลูกค้า' }}</p>
                        <p class="max-w-xs">{{ $sale->customer->address ?? '-' }}</p>
                        <p><span class="text-gray-400">เลขประจำตัวผู้เสียภาษี:</span> {{ $sale->customer->tax_id ?? '-' }}</p>
                        @if($sale->company)
                        <p><span class="text-gray-400">สังกัดบริษัท:</span> {{ $sale->company->name ?? '-' }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 min-w-[280px]">
                    <div class="space-y-3 font-kanit">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">เลขที่เอกสาร:</span>
                            <span class="font-mono font-bold text-gray-900 bg-white px-3 py-1 rounded-lg border border-gray-200">
                                {{ $sale->doc_no }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">วันที่เอกสาร:</span>
                            <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($sale->doc_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">กำหนดชำระ:</span>
                            <span class="font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($sale->doc_date)->addDays($sale->credit_term)->format('d/m/Y') }}
                                @if($sale->credit_term > 0)
                                <span class="text-xs text-gray-400 ml-1">(เครดิต {{ $sale->credit_term }} วัน)</span>
                                @else
                                <span class="text-xs text-gray-400 ml-1">(เงินสด)</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">สาขา:</span>
                            <span class="font-bold text-gray-900">{{ $sale->branch->name ?? 'สำนักงานใหญ่' }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <span class="text-gray-500">สถานะ:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                @if($sale->status == 'ชำระแล้ว') bg-green-100 text-green-700
                                @elseif($sale->status == 'ยกเลิก') bg-gray-100 text-gray-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ $sale->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ITEMS TABLE SECTION --}}
            <div class="mb-10 overflow-x-auto">
                <table class="w-full text-left font-kanit">
                    <thead>
                        <tr class="border-b-2 border-gray-100 text-gray-400 uppercase text-xs tracking-wider">
                            <th class="py-4 px-2 w-16">ลำดับ</th>
                            <th class="py-4 px-2">รายละเอียดสินค้า/บริการ</th>
                            <th class="py-4 px-2 text-center w-24">จำนวน</th>
                            <th class="py-4 px-2 text-right w-32">ราคา/หน่วย</th>
                            <th class="py-4 px-2 text-right w-32">จำนวนเงิน</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($sale->items as $index => $item)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-2 align-top text-center text-gray-400 font-mono">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-2">
                                <p class="font-bold text-gray-900">{{ $item->description }}</p>
                            </td>
                            <td class="py-4 px-2 text-center font-mono">{{ number_format($item->quantity) }}</td>
                            <td class="py-4 px-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-4 px-2 text-right font-mono font-bold text-gray-900">
                                {{ number_format($item->quantity * $item->unit_price, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <i class="fas fa-box-open text-4xl mb-3 block opacity-30"></i>
                                ไม่พบรายการสินค้า
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- NOTE SECTION --}}
            @if($sale->note)
            <div class="mb-8 bg-yellow-50 rounded-xl p-4 border-l-4 border-yellow-400">
                <div class="flex gap-3">
                    <i class="fas fa-sticky-note text-yellow-500 mt-0.5"></i>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-yellow-600 font-bold mb-1">หมายเหตุ</p>
                        <p class="text-sm text-gray-700">{{ $sale->note }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- CALCULATION SUMMARY --}}
            <div class="flex justify-end">
                <div class="w-full md:w-80 space-y-3 font-kanit">
                    <div class="flex justify-between text-gray-600">
                        <span>มูลค่าสินค้า (Subtotal):</span>
                        <span class="font-mono">{{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>ภาษีมูลค่าเพิ่ม (VAT {{ $sale->vat_rate }}%):</span>
                        <span class="font-mono">{{ number_format($sale->vat, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t-2 border-blue-100">
                        <span class="text-xl font-bold text-gray-900">ยอดเงินสุทธิ:</span>
                        <div class="text-right">
                            <span class="text-2xl font-black text-blue-600 font-mono">
                                ฿ {{ number_format($sale->total, 2) }}
                            </span>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase">รวมภาษีมูลค่าเพิ่มแล้ว</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER INFO --}}
            <div class="mt-20 pt-8 border-t border-dashed border-gray-200">
                <div class="grid grid-cols-2 gap-4 text-xs text-gray-400 font-kanit">
                    <div>
                        <p class="mb-1"><i class="fas fa-user-circle mr-1"></i> ผู้ออกเอกสาร: {{ $sale->creator->name ?? 'System Admin' }}</p>
                        <p><i class="fas fa-clock mr-1"></i> บันทึกเมื่อ: {{ $sale->created_at->format('d/m/Y H:i') }} น.</p>
                        @if($sale->updated_at != $sale->created_at)
                        <p><i class="fas fa-edit mr-1"></i> แก้ไขล่าสุด: {{ $sale->updated_at->format('d/m/Y H:i') }} น.</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-500 mb-1">Smart Sign Management System</p>
                        <p>เอกสารนี้ออกโดยระบบอัตโนมัติ ไม่ต้องมีลายเซ็นก็สามารถใช้งานได้</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    .print\:hidden { display: none !important; }
    .rounded-3xl { border-radius: 0 !important; }
    .shadow-2xl { box-shadow: none !important; }
    .bg-gray-50 { background-color: #f9fafb !important; -webkit-print-color-adjust: exact; }
    .bg-gradient-to-r { background: linear-gradient(to right, #2563eb, #4f46e5) !important; -webkit-print-color-adjust: exact; }
    .bg-yellow-50 { background-color: #fefce8 !important; -webkit-print-color-adjust: exact; }
}
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
