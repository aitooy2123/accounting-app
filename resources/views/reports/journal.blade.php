@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl font-kanit">

    {{-- Header --}}
    <div class="mb-6 flex justify-between items-end">

        <div>

            <h1 class="text-3xl font-black tracking-tight">

                @php
                    $currentType = request('journal_type', 'general');
                @endphp

                @if($currentType == 'sales')

                    <span class="text-emerald-600">
                        สมุดรายวันขาย (SJ)
                    </span>

                @elseif($currentType == 'purchase')

                    <span class="text-blue-600">
                        สมุดรายวันซื้อ (PJ)
                    </span>

                @elseif($currentType == 'payment')

                    <span class="text-rose-600">
                        สมุดรายวันจ่าย (PV)
                    </span>

                @elseif($currentType == 'receipt')

                    <span class="text-cyan-600">
                        สมุดรายวันรับ (RV)
                    </span>

                @else

                    <span class="text-gray-800">
                        สมุดรายวันทั่วไป (GJ)
                    </span>

                @endif

            </h1>

            <p class="text-gray-500 mt-1">
                บันทึกรายการบัญชีแยกประเภทเบื้องต้น
            </p>

        </div>

        {{-- Print --}}
        <div class="print:hidden">

            <button onclick="window.print()"
                    class="px-5 py-2 bg-gray-900 text-white rounded-xl hover:bg-black transition-all flex items-center gap-2">

                <i class="fas fa-print"></i>
                พิมพ์รายงาน

            </button>

        </div>

    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 print:hidden">

        <form method="GET"
              action="{{ route('reports.journal') }}">

            {{-- Journal Type --}}
            <div class="flex flex-wrap gap-2 mb-6 p-1.5 bg-gray-50 rounded-2xl w-fit border border-gray-100">

                {{-- General --}}
                <button type="submit"
                        name="journal_type"
                        value="general"
                        class="px-5 py-2 rounded-xl transition-all
                        {{ $currentType == 'general'
                            ? 'bg-white shadow-sm text-gray-900 font-bold'
                            : 'text-gray-400' }}">

                    ทั่วไป

                </button>

                {{-- Sales --}}
                <button type="submit"
                        name="journal_type"
                        value="sales"
                        class="px-5 py-2 rounded-xl transition-all
                        {{ $currentType == 'sales'
                            ? 'bg-emerald-500 text-white shadow-md font-bold'
                            : 'text-gray-400' }}">

                    รายวันขาย

                </button>

                {{-- Purchase --}}
                <button type="submit"
                        name="journal_type"
                        value="purchase"
                        class="px-5 py-2 rounded-xl transition-all
                        {{ $currentType == 'purchase'
                            ? 'bg-blue-600 text-white shadow-md font-bold'
                            : 'text-gray-400' }}">

                    รายวันซื้อ

                </button>

                {{-- Payment --}}
                <button type="submit"
                        name="journal_type"
                        value="payment"
                        class="px-5 py-2 rounded-xl transition-all
                        {{ $currentType == 'payment'
                            ? 'bg-rose-600 text-white shadow-md font-bold'
                            : 'text-gray-400' }}">

                    รายวันจ่าย

                </button>

                {{-- Receipt --}}
                <button type="submit"
                        name="journal_type"
                        value="receipt"
                        class="px-5 py-2 rounded-xl transition-all
                        {{ $currentType == 'receipt'
                            ? 'bg-cyan-600 text-white shadow-md font-bold'
                            : 'text-gray-400' }}">

                    รายวันรับ

                </button>

            </div>

            {{-- Filters --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Start --}}
                <div>

                    <label class="text-xs font-bold text-gray-400 mb-1 block">
                        วันที่เริ่มต้น
                    </label>

                    <input type="date"
                           name="start_date"
                           value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full border-gray-200 rounded-xl focus:ring-emerald-500">

                </div>

                {{-- End --}}
                <div>

                    <label class="text-xs font-bold text-gray-400 mb-1 block">
                        วันที่สิ้นสุด
                    </label>

                    <input type="date"
                           name="end_date"
                           value="{{ request('end_date', now()->format('Y-m-d')) }}"
                           class="w-full border-gray-200 rounded-xl focus:ring-emerald-500">

                </div>

                {{-- Customer --}}
                <div>

                    <label class="text-xs font-bold text-gray-400 mb-1 block">
                        คู่ค้า / ลูกค้า
                    </label>

                    <select name="customer_id"
                            class="w-full border-gray-200 rounded-xl">

                        <option value="">
                            ทั้งหมด
                        </option>

                        @foreach($customers as $customer)

                            <option value="{{ $customer->id }}"
                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>

                                {{ $customer->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Buttons --}}
                <div class="flex items-end gap-2">

                    <button type="submit"
                            class="flex-1 bg-gray-900 text-white py-2 rounded-xl hover:bg-black transition-all">

                        ค้นหา

                    </button>

                    <a href="{{ route('reports.journal') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-500 rounded-xl">

                        <i class="fas fa-undo"></i>

                    </a>

                </div>

            </div>

        </form>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">

        <table class="w-full text-left">

            {{-- Header --}}
            <thead class="
                {{
                    $currentType == 'sales'
                        ? 'bg-emerald-900'
                        : (
                            $currentType == 'purchase'
                                ? 'bg-blue-900'
                                : (
                                    $currentType == 'payment'
                                        ? 'bg-rose-900'
                                        : (
                                            $currentType == 'receipt'
                                                ? 'bg-cyan-900'
                                                : 'bg-gray-900'
                                        )
                                )
                        )
                }}
                text-white text-sm">

                <tr>

                    <th class="px-6 py-4 text-center w-32">
                        วันที่
                    </th>

                    <th class="px-6 py-4 w-40">
                        เลขที่เอกสาร
                    </th>

                    <th class="px-6 py-4">
                        รายการ / ชื่อบัญชี
                    </th>

                    <th class="px-6 py-4 text-right w-40">
                        เดบิต (Dr.)
                    </th>

                    <th class="px-6 py-4 text-right w-40">
                        เครดิต (Cr.)
                    </th>

                </tr>

            </thead>

            {{-- Body --}}
            <tbody class="divide-y divide-gray-100 text-sm">

                @forelse($transactions as $index => $item)

                    {{-- Debit --}}
                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">

                        {{-- Date --}}
                        <td class="px-6 py-4 text-center text-gray-500 align-top"
                            rowspan="{{ $item->vat > 0 ? 3 : 2 }}">

                            {{ date('d/m/Y', strtotime($item->date)) }}

                        </td>

                        {{-- Doc --}}
                        <td class="px-6 py-4 font-mono font-bold text-gray-900 align-top"
                            rowspan="{{ $item->vat > 0 ? 3 : 2 }}">

                            {{ $item->doc_no }}

                        </td>

                        {{-- Debit Account --}}
                        <td class="px-6 py-4">

                            <div class="font-bold text-gray-800">

                                {{ $item->debit_account }}

                            </div>

                            <div class="flex items-center gap-2 mt-1">

                                <div class="text-xs text-gray-400">

                                    {{ $item->customer_name }}

                                </div>

                                {{-- Status --}}
                                @if(($item->status ?? '') == 'ชำระแล้ว')

                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">

                                        ชำระแล้ว

                                    </span>

                                @elseif(($item->status ?? '') == 'ค้างชำระ')

                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">

                                        ค้างชำระ

                                    </span>

                                @endif

                            </div>

                        </td>

                        {{-- Debit --}}
                        <td class="px-6 py-4 text-right font-mono font-bold text-emerald-600">

                            {{ number_format($item->total, 2) }}

                        </td>

                        {{-- Credit --}}
                        <td class="px-6 py-4 text-right text-gray-300">

                            -

                        </td>

                    </tr>

                    {{-- Credit --}}
                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">

                        <td class="px-6 py-2 pl-16">

                            <span class="text-gray-700">

                                {{ $item->credit_account }}

                            </span>

                        </td>

                        <td class="px-6 py-2 text-right text-gray-300">

                            -

                        </td>

                        <td class="px-6 py-2 text-right font-mono font-bold text-rose-600">

                            {{ number_format($item->subtotal, 2) }}

                        </td>

                    </tr>

                    {{-- VAT --}}
                    @if($item->vat > 0)

                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">

                            <td class="px-6 py-2 pl-16 italic text-gray-400">

                                {{
                                    $currentType == 'purchase'
                                        ? 'ภาษีซื้อ'
                                        : 'ภาษีขาย'
                                }}

                                ({{ $item->vat_rate }}%)

                            </td>

                            <td class="px-6 py-2 text-right text-gray-300">

                                -

                            </td>

                            <td class="px-6 py-2 text-right font-mono text-gray-600">

                                {{ number_format($item->vat, 2) }}

                            </td>

                        </tr>

                    @endif

                @empty

                    <tr>

                        <td colspan="5"
                            class="px-6 py-20 text-center text-gray-400">

                            ไม่พบข้อมูลรายการบัญชี

                        </td>

                    </tr>

                @endforelse

            </tbody>

            {{-- Footer --}}
            @if($transactions->count() > 0)

                <tfoot class="bg-gray-50 border-t-2 border-gray-200">

                    <tr>

                        <td colspan="3"
                            class="px-6 py-5 text-right font-bold text-gray-900">

                            รวมยอดดุล (Balanced)

                        </td>

                        <td class="px-6 py-5 text-right font-mono font-black text-emerald-700 text-lg">

                            ฿{{ number_format($totals['total_debit'], 2) }}

                        </td>

                        <td class="px-6 py-5 text-right font-mono font-black text-rose-700 text-lg">

                            ฿{{ number_format($totals['total_credit'], 2) }}

                        </td>

                    </tr>

                </tfoot>

            @endif

        </table>

    </div>

</div>
@endsection
