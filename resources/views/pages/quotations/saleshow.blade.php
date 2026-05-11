{{-- resources/views/sales/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">

    {{-- ACTION BUTTONS TOP --}}
    <div class="mb-6 flex justify-between items-center print:hidden">
        <a href="{{ route('sales.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-all font-kanit">
            <i class="fas fa-arrow-left"></i> กลับหน้ารายการใบเสนอราคา
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-kanit transition-all flex items-center gap-2 shadow-sm">
                <i class="fas fa-print"></i> พิมพ์ใบเสนอราคา
            </button>

            <a href="{{ route('sales.export', $sale->id) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-kanit transition-all flex items-center gap-2 shadow-md">
                <i class="fas fa-file-excel"></i> ส่งออก Excel
            </a>

            <a href="{{ route('sales.edit', $sale) }}" class="px-4 py-2 bg-amber-500 text-white rounded-xl hover:bg-amber-600 font-kanit transition-all flex items-center gap-2 shadow-md">
                <i class="fas fa-edit"></i> แก้ไขข้อมูล
            </a>
        </div>
    </div>

    {{-- MAIN QUOTATION CARD --}}
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden print:shadow-none print:border-0">

        {{-- TOP HEADER BAR (ใช้สีเขียวสำหรับใบเสนอราคา) --}}
        <div class="h-3 bg-gradient-to-r from-emerald-500 to-teal-600"></div>

        <div class="p-8 md:p-12">
            {{-- BRANDING & DOC INFO --}}
            <div class="flex flex-col md:flex-row justify-between gap-8 mb-12">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 font-kanit tracking-tight mb-1">ใบเสนอราคา</h1>
                    <p class="text-gray-500 font-kanit uppercase tracking-widest">Quotation</p>

                    <div class="mt-8 space-y-1 text-sm text-gray-600 font-kanit">
                        <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">ข้อมูลลูกค้า</p>
                        <p class="text-lg text-emerald-700 font-bold">{{ $sale->customer->name ?? 'ไม่ระบุลูกค้า' }}</p>
                        <p class="max-w-xs text-gray-500 leading-relaxed">{{ $sale->customer->address ?? '-' }}</p>
                        <p><span class="text-gray-400">เลขประจำตัวผู้เสียภาษี:</span> {{ $sale->customer->tax_id ?? '-' }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 min-w-[280px]">
                    <div class="space-y-3 font-kanit text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">เลขที่เอกสาร:</span>
                            <span class="font-mono font-bold text-gray-900 bg-white px-3 py-1 rounded-lg border border-gray-200">
                                {{ $sale->doc_no }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">วันที่เสนอราคา:</span>
                            <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($sale->doc_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ยืนราคาภายใน:</span>
                            <span class="font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($sale->doc_date)->addDays($sale->credit_term ?? 30)->format('d/m/Y') }}
                                <span class="text-xs text-gray-400 ml-1">({{ $sale->credit_term ?? 30 }} วัน)</span>
                            </span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <span class="text-gray-500">สถานะ:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                @if($sale->status == 'อนุมัติแล้ว') bg-green-100 text-green-700
                                @elseif($sale->status == 'ยกเลิก') bg-red-100 text-red-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ $sale->status ?? 'รออนุมัติ' }}
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
                            <th class="py-4 px-2 w-16 text-center">ลำดับ</th>
                            <th class="py-4 px-2">รายการสินค้า / รายละเอียด</th>
                            <th class="py-4 px-2 text-center w-24">จำนวน</th>
                            <th class="py-4 px-2 text-right w-32">ราคาต่อหน่วย</th>
                            <th class="py-4 px-2 text-right w-32">จำนวนเงิน</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($sale->items as $index => $item)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-2 align-top text-center text-gray-400 font-mono">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-2">
                                <p class="font-bold text-gray-900">{{ $item->description }}</p>
                                @if($item->note) <p class="text-xs text-gray-400 mt-1">{{ $item->note }}</p> @endif
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
                                ไม่พบรายการในใบเสนอราคา
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY SECTION --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- เงื่อนไขการเสนอราคา --}}
                <div class="text-sm text-gray-500 font-kanit">
                    <h4 class="font-bold text-gray-900 mb-2">เงื่อนไขการเสนอราคา:</h4>
                    <ul class="list-disc list-inside space-y-1">
                        <li>ยืนราคาภายในกำหนดเวลาที่ระบุในเอกสาร</li>
                        <li>ราคานี้รวมภาษีมูลค่าเพิ่ม {{ $sale->vat_rate }}% แล้ว</li>
                        <li>การชำระเงินตามเงื่อนไขที่ตกลงกัน</li>
                    </ul>
                    @if($sale->note)
                    <div class="mt-4 p-4 bg-emerald-50 rounded-xl border-l-4 border-emerald-400 text-emerald-800">
                        <p class="font-bold text-xs uppercase mb-1">หมายเหตุเพิ่มเติม:</p>
                        <p>{{ $sale->note }}</p>
                    </div>
                    @endif
                </div>

                {{-- ยอดรวม --}}
                <div class="space-y-3 font-kanit">
                    <div class="flex justify-between text-gray-600">
                        <span>รวมราคาสินค้า:</span>
                        <span class="font-mono">{{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>ภาษีมูลค่าเพิ่ม ({{ $sale->vat_rate }}%):</span>
                        <span class="font-mono">{{ number_format($sale->vat, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t-2 border-emerald-100">
                        <span class="text-xl font-bold text-gray-900">ยอดเงินสุทธิ:</span>
                        <div class="text-right">
                            <span class="text-2xl font-black text-emerald-600 font-mono">
                                ฿ {{ number_format($sale->total, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SIGNATURE SECTION --}}
            <div class="mt-20 grid grid-cols-2 gap-12 font-kanit">
                <div class="text-center">
                    <div class="border-b border-gray-300 h-16 mb-2"></div>
                    <p class="text-sm font-bold text-gray-800">ผู้สั่งซื้อ / ผู้รับข้อเสนอ</p>
                    <p class="text-xs text-gray-400 italic">วันที่ ......../......../........</p>
                </div>
                <div class="text-center">
                    <div class="border-b border-gray-300 h-16 mb-2"></div>
                    <p class="text-sm font-bold text-gray-800">ผู้อนุมัติเสนอราคา</p>
                    <p class="text-xs text-gray-500">({{ $sale->creator->name ?? 'ฝ่ายขาย' }})</p>
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
    .bg-gradient-to-r { background: linear-gradient(to right, #10b981, #0d9488) !important; -webkit-print-color-adjust: exact; }
}
</style>
@endsection
