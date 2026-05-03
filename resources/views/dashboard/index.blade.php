{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">ภาพรวมธุรกิจ (Dashboard)</h1>
            <p class="text-sm text-gray-500 font-kanit">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="px-3 py-2 border rounded-xl text-sm">
                <span class="text-gray-400">ถึง</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="px-3 py-2 border rounded-xl text-sm">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-kanit">กรอง</button>
            </form>
        </div>
    </div>

    {{-- ROW 1: 4 Big Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <p class="text-sm text-gray-500 font-kanit">รายรับ (วันนี้)</p>
            <p class="text-2xl font-bold text-green-600 font-kanit">฿ {{ number_format($todayIncome, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <p class="text-sm text-gray-500 font-kanit">รายจ่าย (วันนี้)</p>
            <p class="text-2xl font-bold text-red-500 font-kanit">฿ {{ number_format($todayExpense, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <p class="text-sm text-gray-500 font-kanit">กำไร (วันนี้)</p>
            <p class="text-2xl font-bold text-blue-600 font-kanit">฿ {{ number_format($todayProfit, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <p class="text-sm text-gray-500 font-kanit">ค้างชำระทั้งหมด</p>
            <p class="text-2xl font-bold text-orange-500 font-kanit">{{ ($unpaidInvoices['count'] ?? 0) + ($unpaidPurchases['count'] ?? 0) }} รายการ</p>
            <p class="text-xs text-orange-400">มูลค่า ฿ {{ number_format(($unpaidInvoices['total_amount'] ?? 0) + ($unpaidPurchases['total_amount'] ?? 0), 2) }}</p>
        </div>
    </div>

    {{-- ROW 2: Mini Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
            <i class="fas fa-users text-2xl mb-1 opacity-80"></i>
            <p class="text-xl font-bold">{{ $totalCustomers }}</p>
            <p class="text-xs opacity-90">ลูกค้า</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <i class="fas fa-user-plus text-2xl mb-1 opacity-80"></i>
            <p class="text-xl font-bold">{{ $newCustomersThisMonth }}</p>
            <p class="text-xs opacity-90">ลูกค้าใหม่</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
            <i class="fas fa-store-alt text-2xl mb-1 opacity-80"></i>
            <p class="text-xl font-bold">{{ $systemSummary['branches'] }}</p>
            <p class="text-xs opacity-90">สาขา</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-4 text-white">
            <i class="fas fa-building text-2xl mb-1 opacity-80"></i>
            <p class="text-xl font-bold">{{ $systemSummary['companies'] }}</p>
            <p class="text-xs opacity-90">บริษัท</p>
        </div>
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl p-4 text-white">
            <i class="fas fa-file-invoice-dollar text-2xl mb-1 opacity-80"></i>
            <p class="text-xl font-bold">{{ $systemSummary['accounts'] }}</p>
            <p class="text-xs opacity-90">รหัสบัญชี</p>
        </div>
    </div>

    {{-- ROW 3: Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border p-5">
            <h3 class="text-sm font-bold mb-4"><i class="fas fa-chart-line text-blue-500 mr-2"></i>รายรับ-รายจ่าย 7 วัน</h3>
            <canvas id="incomeExpenseChart" height="100"></canvas>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <h3 class="text-sm font-bold mb-4"><i class="fas fa-chart-pie text-purple-500 mr-2"></i>สัดส่วนรายจ่าย</h3>
            <canvas id="expenseCategoryChart" height="200"></canvas>
            <div id="expenseLegend" class="mt-3 text-xs space-y-1"></div>
        </div>
    </div>

    {{-- ROW 4: Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Recent Sales --}}
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <div class="p-4 border-b flex justify-between">
                <h3 class="text-sm font-bold"><i class="fas fa-receipt text-blue-500 mr-2"></i>รายการขายล่าสุด</h3>
                <a href="{{ route('sales.index') }}" class="text-xs text-blue-600">ดูทั้งหมด</a>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr><th class="px-4 py-2">เลขที่</th><th class="px-4 py-2">ลูกค้า</th><th class="px-4 py-2 text-right">จำนวนเงิน</th><th class="px-4 py-2 text-center">สถานะ</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentSales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-blue-600">{{ $sale->doc_no }}</td>
                        <td class="px-4 py-2">{{ $sale->customer_name }}</td>
                        <td class="px-4 py-2 text-right">฿ {{ number_format($sale->total, 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $sale->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">{{ $sale->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-8 text-gray-400">ไม่มีรายการ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Recent Purchases --}}
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <div class="p-4 border-b flex justify-between">
                <h3 class="text-sm font-bold"><i class="fas fa-shopping-cart text-purple-500 mr-2"></i>รายการซื้อล่าสุด</h3>
                <a href="{{ route('purchases.index') }}" class="text-xs text-blue-600">ดูทั้งหมด</a>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr><th class="px-4 py-2">เลขที่</th><th class="px-4 py-2">ผู้ขาย</th><th class="px-4 py-2 text-right">จำนวนเงิน</th><th class="px-4 py-2 text-center">สถานะ</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentPurchases as $purchase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-purple-600">{{ $purchase->doc_no }}</td>
                        <td class="px-4 py-2">{{ $purchase->supplier_name }}</td>
                        <td class="px-4 py-2 text-right">฿ {{ number_format($purchase->total, 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $purchase->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">{{ $purchase->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-8 text-gray-400">ไม่มีรายการ</td></tr>
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

    // Income vs Expense Chart
    new Chart(document.getElementById('incomeExpenseChart'), {
        type: 'line',
        data: {
            labels: incomeExpenseData.labels,
            datasets: [
                { label: 'รายรับ', data: incomeExpenseData.income, borderColor: '#10B981', backgroundColor: 'rgba(16,185,129,0.1)', tension: 0.3, fill: true },
                { label: 'รายจ่าย', data: incomeExpenseData.expense, borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,0.1)', tension: 0.3, fill: true }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { ticks: { callback: v => '฿ ' + v.toLocaleString() } } } }
    });

    // Expense Category Chart
    new Chart(document.getElementById('expenseCategoryChart'), {
        type: 'doughnut',
        data: { labels: expenseCategoryData.labels, datasets: [{ data: expenseCategoryData.data, backgroundColor: expenseCategoryData.colors }] },
        options: { responsive: true, cutout: '65%', plugins: { legend: { display: false } } }
    });

    // Legend
    const legend = document.getElementById('expenseLegend');
    const total = expenseCategoryData.data.reduce((a,b) => a+b, 0) || 1;
    expenseCategoryData.labels.forEach((label, i) => {
        const pct = ((expenseCategoryData.data[i]/total)*100).toFixed(1);
        legend.innerHTML += `<div class="flex justify-between"><span class="text-gray-500"><span class="w-2 h-2 inline-block rounded-full mr-1" style="background:${expenseCategoryData.colors[i]}"></span>${label}</span><span class="font-bold">${pct}%</span></div>`;
    });
});
</script>
@endpush
@endsection
