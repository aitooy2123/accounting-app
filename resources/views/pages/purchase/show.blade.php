{{-- resources/views/purchases/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">

    {{-- ACTION BUTTONS TOP --}}
    <div class="mb-6 flex justify-between items-center print:hidden">
        <a href="{{ route('purchases.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-purple-600 transition-all font-kanit">
            <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-kanit transition-all flex items-center gap-2 shadow-sm">
                <i class="fas fa-print"></i> พิมพ์เอกสาร
            </button>
            <a href="{{ route('purchases.edit', $purchase) }}" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-kanit transition-all flex items-center gap-2 shadow-md">
                <i class="fas fa-edit"></i> แก้ไขข้อมูล
            </a>
        </div>
    </div>

    {{-- MAIN INVOICE CARD --}}
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden print:shadow-none print:border-0">

        {{-- TOP HEADER BAR --}}
        <div class="h-3 bg-gradient-to-r from-purple-600 to-indigo-600"></div>

        <div class="p-8 md:p-12">
            {{-- BRANDING & DOC INFO --}}
            <div class="flex flex-col md:flex-row justify-between gap-8 mb-12">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 font-kanit tracking-tight mb-1">ใบกำกับภาษีซื้อ</h1>
                    <p class="text-gray-500 font-kanit">Purchase Tax Invoice</p>

                    <div class="mt-8 space-y-1 text-sm text-gray-600 font-kanit">
                        <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">ข้อมูลผู้ขาย</p>
                        <p class="text-lg text-purple-700 font-bold">{{ $purchase->supplier->name ?? 'ไม่ระบุผู้ขาย' }}</p>
                        <p class="max-w-xs">{{ $purchase->supplier->address ?? '-' }}</p>
                        <p><span class="text-gray-400">เลขประจำตัวผู้เสียภาษี:</span> {{ $purchase->supplier->tax_id ?? '-' }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 min-w-[280px]">
                    <div class="space-y-3 font-kanit">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">เลขที่เอกสาร:</span>
                            <span class="font-mono font-bold text-gray-900 bg-white px-3 py-1 rounded-lg border border-gray-200">
                                {{ $purchase->doc_no }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">วันที่เอกสาร:</span>
                            <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($purchase->doc_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">สาขา:</span>
                            <span class="font-bold text-gray-900">{{ $purchase->branch->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <span class="text-gray-500">สถานะ:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                @if($purchase->status == 'ชำระแล้ว') bg-green-100 text-green-700
                                @elseif($purchase->status == 'ยกเลิก') bg-gray-100 text-gray-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ $purchase->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE SECTION --}}
            <div class="mb-10 overflow-x-auto">
                <table class="w-full text-left font-kanit">
                    <thead>
                        <tr class="border-b-2 border-gray-100 text-gray-400 uppercase text-xs tracking-wider">
                            <th class="py-4 px-2 w-16">ลำดับ</th>
                            <th class="py-4 px-2">รายละเอียดรายการซื้อ</th>
                            <th class="py-4 px-2 text-right">จำนวนเงิน (ก่อนภาษี)</th>
                            <th class="py-4 px-2 text-right">ภาษี ({{ $purchase->vat_rate }}%)</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-6 px-2 align-top text-center text-gray-400 font-mono">01</td>
                            <td class="py-6 px-2">
                                <p class="font-bold text-gray-900">บันทึกค่าใช้จ่าย/ซื้อสินค้า</p>
                                <div class="mt-2 text-sm text-gray-500 bg-gray-50 p-3 rounded-lg border border-dashed border-gray-200">
                                    <i class="fas fa-sticky-note mr-2 text-purple-300"></i>
                                    {{ $purchase->note ?: 'ไม่มีหมายเหตุเพิ่มเติม' }}
                                </div>
                            </td>
                            <td class="py-6 px-2 text-right font-mono font-bold text-gray-900">
                                {{ number_format($purchase->subtotal, 2) }}
                            </td>
                            <td class="py-6 px-2 text-right font-mono text-gray-500">
                                {{ number_format($purchase->vat, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- CALCULATION SUMMARY --}}
            <div class="flex justify-end">
                <div class="w-full md:w-80 space-y-3 font-kanit">
                    <div class="flex justify-between text-gray-600">
                        <span>มูลค่าฐานภาษี:</span>
                        <span class="font-mono">{{ number_format($purchase->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>ภาษีมูลค่าเพิ่ม ({{ $purchase->vat_rate }}%):</span>
                        <span class="font-mono">{{ number_format($purchase->vat, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t-2 border-purple-100">
                        <span class="text-xl font-bold text-gray-900">ยอดเงินสุทธิ:</span>
                        <div class="text-right">
                            <span class="text-2xl font-black text-purple-600 font-mono">
                                ฿ {{ number_format($purchase->total, 2) }}
                            </span>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase">Inclusive of VAT</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER INFO --}}
            <div class="mt-20 pt-8 border-t border-dashed border-gray-200">
                <div class="grid grid-cols-2 gap-4 text-xs text-gray-400 font-kanit">
                    <div>
                        <p class="mb-1"><i class="fas fa-user-circle mr-1"></i> ผู้ออกเอกสาร: {{ $purchase->creator->name ?? 'System Admin' }}</p>
                        <p><i class="fas fa-clock mr-1"></i> บันทึกเมื่อ: {{ $purchase->created_at->format('d/m/Y H:i') }} น.</p>
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
    .bg-gradient-to-r { background: linear-gradient(to right, #9333ea, #4f46e5) !important; -webkit-print-color-adjust: exact; }
}
</style>
@endsection
