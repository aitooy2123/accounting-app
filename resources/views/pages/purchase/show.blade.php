<x-app-layout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('purchases.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-chevron-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">รายละเอียดใบสั่งซื้อ</h1>
            </div>
            <p class="text-sm text-gray-500 font-kanit ml-8">เลขที่เอกสาร: <span class="font-bold text-blue-600">{{ $purchase->doc_no }}</span></p>
        </div>
{{-- ค้นหาจุดที่มีปุ่มแก้ไขข้อมูล แล้วเพิ่มปุ่ม Export ต่อท้าย --}}
<div class="flex space-x-3 ml-8 md:ml-0">
    <button onclick="window.print()" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
        <i class="fas fa-print mr-2"></i> พิมพ์เอกสาร
    </button>

    <a href="{{ route('purchases.export', $purchase->id) }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-emerald-200 font-kanit">
        <i class="fas fa-file-excel mr-2"></i> Export Excel
    </a>

    <a href="{{ route('purchases.edit', $purchase->id) }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200 font-kanit">
        <i class="fas fa-edit mr-2"></i> แก้ไขข้อมูล
    </a>
</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- ส่วนข้อมูลหลัก --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- การ์ดข้อมูลผู้จำหน่ายและสาขา --}}
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-6">
                    @if($purchase->status == 'ชำระแล้ว')
                        <span class="px-4 py-2 bg-emerald-100 text-emerald-600 rounded-full text-sm font-bold uppercase tracking-wide">
                            <i class="fas fa-check-circle mr-1"></i> {{ $purchase->status }}
                        </span>
                    @elseif($purchase->status == 'ยกเลิก')
                        <span class="px-4 py-2 bg-red-100 text-red-600 rounded-full text-sm font-bold uppercase tracking-wide">
                            <i class="fas fa-times-circle mr-1"></i> {{ $purchase->status }}
                        </span>
                    @else
                        <span class="px-4 py-2 bg-orange-100 text-orange-600 rounded-full text-sm font-bold uppercase tracking-wide">
                            <i class="fas fa-clock mr-1"></i> {{ $purchase->status }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">ข้อมูลผู้จำหน่าย</h3>
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-50 p-3 rounded-2xl text-blue-600">
                                <i class="fas fa-truck text-xl"></i>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-900 font-kanit">{{ $purchase->supplier->name ?? 'ไม่ระบุ' }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $purchase->supplier->address ?? 'ไม่มีข้อมูลที่อยู่' }}</p>
                                <p class="text-sm text-gray-500">{{ $purchase->supplier->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">สถานที่รับสินค้า</h3>
                        <div class="flex items-start space-x-3">
                            <div class="bg-emerald-50 p-3 rounded-2xl text-emerald-600">
                                <i class="fas fa-store text-xl"></i>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-900 font-kanit">{{ $purchase->branch->name ?? 'ไม่ระบุ' }}</p>
                                <p class="text-sm text-gray-500 mt-1">คลังสินค้า/สาขาที่รับเข้าสต็อก</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($purchase->note)
                <div class="mt-8 pt-6 border-t border-gray-50">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">หมายเหตุเพิ่มเติม</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $purchase->note }}</p>
                </div>
                @endif
            </div>

            {{-- ตารางรายการสินค้า --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center space-x-2 text-blue-600 font-bold">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="font-kanit tracking-wide">รายการสินค้าในคำสั่งซื้อ</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">#</th>
                                <th class="px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">รายละเอียดสินค้า</th>
                                <th class="px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">จำนวน</th>
                                <th class="px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">ราคา/หน่วย</th>
                                <th class="px-8 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">ยอดรวม</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($purchase->items as $index => $item)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-8 py-4 text-sm text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-bold text-gray-800 font-kanit">{{ $item->desc }}</p>
                                </td>
                                <td class="px-4 py-4 text-center text-sm font-medium text-gray-600">
                                    {{ number_format($item->qty, 0) }}
                                </td>
                                <td class="px-4 py-4 text-right text-sm text-gray-600">
                                    {{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-8 py-4 text-right text-sm font-bold text-gray-900">
                                    {{ number_format($item->total, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- สรุปยอดเงิน (Sidebar) --}}
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">รายละเอียดวันที่</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 font-kanit">วันที่เอกสาร:</span>
                        <span class="text-sm font-bold text-gray-800">{{ $purchase->doc_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 font-kanit">วันที่ครบกำหนด:</span>
                        <span class="text-sm font-bold text-red-500">{{ $purchase->due_date ? $purchase->due_date->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="pt-4 border-t border-gray-50">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-kanit">สร้างเมื่อ:</span>
                            <span class="text-xs text-gray-400">{{ $purchase->created_at->format('d/m/Y H:i') }} น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-8 rounded-[2rem] shadow-xl text-white relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10 text-9xl">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>

                <h3 class="text-xs font-bold uppercase tracking-[0.2em] opacity-70 mb-6">สรุปยอดรวม (Summary)</h3>

                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between text-sm">
                        <span class="opacity-80">ยอดรวมสินค้า (Subtotal)</span>
                        <span class="font-bold">{{ number_format($purchase->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="opacity-80">ภาษีมูลค่าเพิ่ม (VAT {{ (int)$purchase->vat_rate }}%)</span>
                        <span class="font-bold">{{ number_format($purchase->vat, 2) }}</span>
                    </div>

                    <div class="pt-6 mt-6 border-t border-white/20">
                        <p class="text-xs opacity-70 uppercase mb-1">ยอดชำระสุทธิทั้งสิ้น</p>
                        <div class="flex justify-between items-end">
                            <span class="text-4xl font-bold tracking-tight">{{ number_format($purchase->total, 2) }}</span>
                            <span class="text-sm font-medium opacity-80 mb-1">THB</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-center space-x-3">
                <div class="bg-blue-600 text-white p-2 rounded-lg">
                    <i class="fas fa-info-circle"></i>
                </div>
                <p class="text-xs text-blue-700 font-kanit leading-snug">
                    เอกสารนี้เป็นใบสั่งซื้อที่ได้รับการบันทึกเข้าสู่ระบบแล้ว ข้อมูลสต็อกจะถูกอัปเดตตามสาขาที่ระบุ
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
