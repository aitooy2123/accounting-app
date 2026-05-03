@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 font-kanit">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">รายละเอียดลูกค้า</h1>
            <p class="text-sm text-gray-500">ตรวจสอบข้อมูลพื้นฐาน ประวัติธุรกรรม และรายงานทางบัญชี</p>
        </div>

       <div class="flex flex-wrap gap-2 w-full lg:w-auto">
    <a href="{{ route('customers.index') }}"
       class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-all shadow-sm">
        <i class="fas fa-arrow-left mr-2"></i> กลับ
    </a>

    <div class="flex flex-wrap gap-2">
        @php
            $reportLinks = [
                ['route' => 'reports.journal', 'icon' => 'fa-book', 'title' => 'สมุดรายวัน', 'color' => 'blue'],
                ['route' => 'reports.ledger', 'icon' => 'fa-list-ul', 'title' => 'แยกประเภท', 'color' => 'indigo'],
                ['route' => 'reports.trial-balance', 'icon' => 'fa-scale-balanced', 'title' => 'งบทดลอง', 'color' => 'emerald'],
                ['route' => 'reports.pnl', 'icon' => 'fa-chart-line', 'title' => 'กำไรขาดทุน', 'color' => 'rose'],
            ];
        @endphp

        @foreach($reportLinks as $report)
            <a href="{{ route($report['route'], ['customer_id' => $customer->id]) }}"
               class="inline-flex items-center px-3 py-2 bg-white border border-gray-100 text-gray-600 hover:text-{{ $report['color'] }}-600 hover:bg-{{ $report['color'] }}-50 rounded-xl transition-all shadow-sm text-xs font-bold group">
                <i class="fas {{ $report['icon'] }} mr-2 text-gray-400 group-hover:text-{{ $report['color'] }}-500 transition-colors"></i>
                {{ $report['title'] }}
            </a>
        @endforeach
    </div>

    <a href="{{ route('customers.edit', $customer) }}"
       class="flex-1 lg:flex-none inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-100">
        <i class="fas fa-edit mr-2"></i> แก้ไขข้อมูล
    </a>
</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase">ยอดขายสะสม</p>
            <p class="text-xl font-bold text-gray-900 mt-1">฿ {{ number_format($customer->sales->sum('total_amount'), 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase text-red-500">ยอดค้างชำระ (AR)</p>
            <p class="text-xl font-bold text-red-600 mt-1">฿ {{ number_format($customer->balance ?? 0, 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase">จำนวนบิลทั้งหมด</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $customer->sales->count() }} รายการ</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">รหัสลูกค้า: <span class="text-blue-600 ml-1">{{ $customer->code }}</span></span>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold {{ $customer->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $customer->is_active ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                {{ $customer->is_active ? 'เปิดใช้งาน' : 'ระงับการใช้งาน' }}
            </span>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-10">
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center">
                        <span class="w-1 h-4 bg-blue-500 rounded-full mr-2"></span> ข้อมูลพื้นฐาน
                    </h3>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase">ชื่อลูกค้า / นามนิติบุคคล</label>
                            <p class="text-gray-900 font-semibold text-lg mt-0.5">{{ $customer->name }}</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase">เลขประจำตัวผู้เสียภาษี</label>
                            <p class="text-gray-900 font-medium mt-0.5 tracking-wider">{{ $customer->tax_id ?: 'ไม่ได้ระบุ' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">สังกัดบริษัท</label>
                                <p class="text-gray-800 text-sm font-medium">{{ $customer->company->name ?? '-' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">สาขา</label>
                                <p class="text-gray-800 text-sm font-medium">{{ $customer->branch->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center">
                        <span class="w-1 h-4 bg-purple-500 rounded-full mr-2"></span> ข้อมูลการติดต่อ
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-start p-3 hover:bg-gray-50 rounded-xl transition-colors">
                            <div class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center mr-3 mt-1">
                                <i class="fas fa-phone-alt text-xs"></i>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">เบอร์โทรศัพท์</label>
                                <p class="text-gray-900 font-medium">{{ $customer->phone ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start p-3 hover:bg-gray-50 rounded-xl transition-colors">
                            <div class="w-8 h-8 bg-purple-50 text-purple-500 rounded-lg flex items-center justify-center mr-3 mt-1">
                                <i class="fas fa-envelope text-xs"></i>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">อีเมล</label>
                                <p class="text-gray-900 font-medium">{{ $customer->email ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start p-3 hover:bg-gray-50 rounded-xl transition-colors">
                            <div class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center mr-3 mt-1">
                                <i class="fas fa-map-marker-alt text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">ที่อยู่จัดส่งเอกสาร</label>
                                <p class="text-gray-900 font-medium leading-relaxed mt-0.5">{{ $customer->address ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex space-x-6">
                    <div class="text-[11px] text-gray-400">
                        <i class="far fa-calendar-plus mr-1"></i> เพิ่มเข้าระบบ: {{ $customer->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="text-[11px] text-gray-400">
                        <i class="far fa-edit mr-1"></i> แก้ไขล่าสุด: {{ $customer->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                @if($customer->created_by)
                <div class="text-[11px] text-gray-400 italic">
                    ผู้บันทึก: {{ $customer->creator->name ?? 'System' }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 italic text-sm">ข้อมูลรายการขาย</h3>
                <a href="#" class="text-xs font-bold text-blue-600 hover:underline">ดูทั้งหมด</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-3">เอกสาร</th>
                            <th class="px-6 py-3 text-right">จำนวนเงิน</th>
                            <th class="px-6 py-3 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($customer->sales->sortByDesc('created_at')->take(5) as $sale)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-800">{{ $sale->invoice_no }}</div>
                                <div class="text-[10px] text-gray-400">{{ $sale->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-700">
                                {{ number_format($sale->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $sale->status_color }}">
                                    {{ $sale->status_text }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-gray-200 text-3xl mb-2 block"></i>
                                <span class="text-gray-400 text-xs italic">ไม่มีข้อมูลการขาย</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 italic text-sm">ข้อมูลรายการซื้อ</h3>
                <a href="#" class="text-xs font-bold text-purple-600 hover:underline">ดูทั้งหมด</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-3">เอกสาร</th>
                            <th class="px-6 py-3 text-right">จำนวนเงิน</th>
                            <th class="px-6 py-3 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($customer->purchases->sortByDesc('created_at')->take(5) as $purchase)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-800">{{ $purchase->po_no }}</div>
                                <div class="text-[10px] text-gray-400">{{ $purchase->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-700">
                                {{ number_format($purchase->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $purchase->status_color }}">
                                    {{ $purchase->status_text }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <i class="fas fa-shopping-bag text-gray-200 text-3xl mb-2 block"></i>
                                <span class="text-gray-400 text-xs italic">ไม่มีข้อมูลการซื้อ</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
