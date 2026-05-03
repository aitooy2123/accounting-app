{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">หน้าหลัก (Dashboard)</h1>
            <p class="text-sm text-gray-500 font-kanit">ภาพรวมข้อมูล ณ วันที่ {{ now()->format('d/m/Y') }} เวลา {{ now()->format('H:i') }} น.</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <form method="GET" class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-kanit">
                <span class="text-gray-400">ถึง</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-kanit">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-kanit hover:bg-blue-700">
                    <i class="fas fa-filter mr-1"></i>กรอง
                </button>
                <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </form>
            <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-kanit hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>สร้างเอกสารขาย
            </a>
        </div>
    </div>

    {{-- ROW 1: BIG NUMBER CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @include('dashboard.partials.stat-card', [
            'title' => 'รายรับ (วันนี้)',
            'value' => '฿ ' . number_format($todayIncome, 2),
            'icon' => 'fa-arrow-trend-up',
            'color' => 'green',
            'subtitle' => 'ข้อมูลวันนี้'
        ])

        @include('dashboard.partials.stat-card', [
            'title' => 'รายจ่าย (วันนี้)',
            'value' => '฿ ' . number_format($todayExpense, 2),
            'icon' => 'fa-arrow-trend-down',
            'color' => 'red',
            'subtitle' => 'ข้อมูลวันนี้'
        ])

        @include('dashboard.partials.stat-card', [
            'title' => 'กำไรสุทธิ',
            'value' => '฿ ' . number_format($todayProfit, 2),
            'icon' => 'fa-chart-pie',
            'color' => 'blue',
            'subtitle' => ($todayIncome > 0 ? round(($todayProfit/$todayIncome)*100, 1) : 0) . '% Margin'
        ])

        @include('dashboard.partials.stat-card', [
            'title' => 'ค้างชำระ',
            'value' => ($unpaidInvoices['count'] ?? 0) . ' ใบ',
            'icon' => 'fa-file-invoice',
            'color' => 'orange',
            'subtitle' => 'มูลค่า ฿ ' . number_format($unpaidInvoices['total_amount'] ?? 0, 2)
        ])
    </div>

    {{-- ROW 2: MINI SUMMARY --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
            <i class="fas fa-users text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $totalCustomers }}</p>
            <p class="text-sm opacity-90 font-kanit">ลูกค้าทั้งหมด</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <i class="fas fa-user-plus text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $newCustomersThisMonth }}</p>
            <p class="text-sm opacity-90 font-kanit">ลูกค้าใหม่เดือนนี้</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
            <i class="fas fa-store-alt text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $systemSummary['branches'] }}</p>
            <p class="text-sm opacity-90 font-kanit">สาขา</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-4 text-white">
            <i class="fas fa-building text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $systemSummary['companies'] }}</p>
            <p class="text-sm opacity-90 font-kanit">บริษัท</p>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 font-kanit mb-4">
                <i class="fas fa-chart-line text-blue-500 mr-2"></i>รายรับ - รายจ่าย (7 วันล่าสุด)
            </h3>
            <canvas id="incomeExpenseChart" height="100"></canvas>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 font-kanit mb-4">
                <i class="fas fa-chart-pie text-purple-500 mr-2"></i>สัดส่วนรายจ่ายตามหมวด
            </h3>
            <canvas id="expenseCategoryChart" height="200"></canvas>
            <div id="expenseLegend" class="mt-4 space-y-1 text-xs font-kanit"></div>
        </div>
    </div>

    {{-- TABLES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Recent Sales --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-sm font-bold font-kanit">รายการขายล่าสุด</h3>
                <a href="{{ route('sales.index') }}" class="text-xs text-blue-600 hover:underline font-kanit">ดูทั้งหมด</a>
            </div>
            <table class="w-full text-sm font-kanit">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="px-4 py-2 text-left">เลขที่</th>
                        <th class="px-4 py-2 text-left">ลูกค้า</th>
                        <th class="px-4 py-2 text-right">จำนวนเงิน</th>
                        <th class="px-4 py-2 text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentSales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-blue-600">{{ $sale->doc_no }}</td>
                        <td class="px-4 py-2">{{ $sale->customer_name }}</td>
                        <td class="px-4 py-2 text-right">฿ {{ number_format($sale->total, 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $sale->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-8 text-gray-400">ยังไม่มีรายการขาย</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Recent Customers --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-sm font-bold font-kanit">ลูกค้าล่าสุด</h3>
                <a href="{{ route('customers.index') }}" class="text-xs text-blue-600 hover:underline font-kanit">ดูทั้งหมด</a>
            </div>
            <table class="w-full text-sm font-kanit">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="px-4 py-2 text-left">รหัส</th>
                        <th class="px-4 py-2 text-left">ชื่อ</th>
                        <th class="px-4 py-2 text-left">บริษัท</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentCustomers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-blue-600">{{ $customer->code }}</td>
                        <td class="px-4 py-2">{{ $customer->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $customer->company->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-8 text-gray-400">ยังไม่มีข้อมูลลูกค้า</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const incomeExpenseData = @json($incomeExpenseChart);
    const expenseCategoryData = @json($expenseByCategory);

    // Chart 1: Income vs Expense
    new Chart(document.getElementById('incomeExpenseChart'), {
        type: 'line',
        data: {
            labels: incomeExpenseData.labels,
            datasets: [{
                label: 'รายรับ', data: incomeExpenseData.income,
                borderColor: '#10B981', backgroundColor: 'rgba(16,185,129,0.1)',
                tension: 0.3, fill: true
            }, {
                label: 'รายจ่าย', data: incomeExpenseData.expense,
                borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,0.1)',
                tension: 0.3, fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { ticks: { callback: v => '฿ ' + v.toLocaleString() } } }
        }
    });

    // Chart 2: Expense Category
    new Chart(document.getElementById('expenseCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: expenseCategoryData.labels,
            datasets: [{
                data: expenseCategoryData.data,
                backgroundColor: expenseCategoryData.colors
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    // Generate legend
    const legend = document.getElementById('expenseLegend');
    const total = expenseCategoryData.data.reduce((a,b) => a+b, 0);
    expenseCategoryData.labels.forEach((label, i) => {
        const pct = ((expenseCategoryData.data[i]/total)*100).toFixed(1);
        legend.innerHTML += `
            <div class="flex justify-between">
                <span class="text-gray-500">
                    <span class="w-2 h-2 inline-block rounded-full mr-1" style="background:${expenseCategoryData.colors[i]}"></span>${label}
                </span>
                <span class="font-bold">${pct}%</span>
            </div>`;
    });
});
</script>
@endpush
@endsection
