{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">ภาพรวมธุรกิจ</h1>
                <p class="text-sm text-gray-500 font-kanit mt-1">อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i') }} น.</p>
            </div>
            <form method="GET" class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl p-1">
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="pl-9 pr-3 py-2 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <span class="text-gray-400 text-sm">—</span>
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="pl-9 pr-3 py-2 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-1 text-xs"></i>กรอง
                </button>
            </form>
        </div>
    </div>

    {{-- ROW 1: 4 Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-500 font-medium">รายรับ (วันนี้)</p>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-arrow-up text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">฿ {{ number_format($todayIncome, 2) }}</p>
            <p class="text-xs text-green-600 mt-2">▲ +12% จากเมื่อวาน</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-500 font-medium">รายจ่าย (วันนี้)</p>
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-arrow-down text-red-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">฿ {{ number_format($todayExpense, 2) }}</p>
            <p class="text-xs text-red-600 mt-2">▼ -5% จากเมื่อวาน</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-500 font-medium">กำไร (วันนี้)</p>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">฿ {{ number_format($todayProfit, 2) }}</p>
            <p class="text-xs text-blue-600 mt-2">▲ +8% จากเมื่อวาน</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-500 font-medium">ค้างชำระทั้งหมด</p>
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ ($unpaidInvoices['count'] ?? 0) + ($unpaidPurchases['count'] ?? 0) }} รายการ</p>
            <p class="text-xs text-orange-600 mt-2">มูลค่า ฿ {{ number_format(($unpaidInvoices['total_amount'] ?? 0) + ($unpaidPurchases['total_amount'] ?? 0), 2) }}</p>
        </div>
    </div>

    {{-- ROW 2: Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalCustomers }}</p>
                    <p class="text-xs text-gray-500">ลูกค้า</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-plus text-xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $newCustomersThisMonth }}</p>
                    <p class="text-xs text-gray-500">ลูกค้าใหม่</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-store-alt text-xl text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $systemSummary['branches'] }}</p>
                    <p class="text-xs text-gray-500">สาขา</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-xl text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $systemSummary['companies'] }}</p>
                    <p class="text-xs text-gray-500">บริษัท</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-pink-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-xl text-pink-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $systemSummary['accounts'] }}</p>
                    <p class="text-xs text-gray-500">รหัสบัญชี</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3: Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900">
                    <i class="fas fa-chart-line text-blue-500 mr-2"></i>รายรับ-รายจ่าย 7 วัน
                </h3>
                <div class="flex gap-3">
                    <span class="flex items-center text-xs"><span class="w-3 h-3 bg-green-500 rounded-full mr-1"></span>รายรับ</span>
                    <span class="flex items-center text-xs"><span class="w-3 h-3 bg-red-500 rounded-full mr-1"></span>รายจ่าย</span>
                </div>
            </div>
            <canvas id="incomeExpenseChart" height="100"></canvas>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-5">
                <i class="fas fa-chart-pie text-purple-500 mr-2"></i>สัดส่วนรายจ่าย
            </h3>
            <canvas id="expenseCategoryChart" height="200"></canvas>
            <div id="expenseLegend" class="mt-4 space-y-2"></div>
        </div>
    </div>

    {{-- ROW 4: Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Sales --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900">
                        <i class="fas fa-receipt text-blue-500 mr-2"></i>รายการขายล่าสุด
                    </h3>
                    <a href="{{ route('sales.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        ดูทั้งหมด <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">เลขที่</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">ลูกค้า</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">จำนวนเงิน</th>
                            <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentSales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 font-mono text-sm text-blue-600">{{ $sale->doc_no }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $sale->customer_name }}</td>
                            <td class="px-5 py-3 text-right font-medium text-gray-900">฿ {{ number_format($sale->total, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $sale->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    <i class="fas {{ $sale->status == 'ชำระแล้ว' ? 'fa-check-circle' : 'fa-clock' }} mr-1 text-xs"></i>
                                    {{ $sale->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p class="text-sm">ไม่มีรายการขาย</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Purchases --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900">
                        <i class="fas fa-shopping-cart text-purple-500 mr-2"></i>รายการซื้อล่าสุด
                    </h3>
                    <a href="{{ route('purchases.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                        ดูทั้งหมด <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">เลขที่</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">ผู้ขาย</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">จำนวนเงิน</th>
                            <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentPurchases as $purchase)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 font-mono text-sm text-purple-600">{{ $purchase->doc_no }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $purchase->supplier_name }}</td>
                            <td class="px-5 py-3 text-right font-medium text-gray-900">฿ {{ number_format($purchase->total, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $purchase->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    <i class="fas {{ $purchase->status == 'ชำระแล้ว' ? 'fa-check-circle' : 'fa-clock' }} mr-1 text-xs"></i>
                                    {{ $purchase->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p class="text-sm">ไม่มีรายการซื้อ</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                {
                    label: 'รายรับ',
                    data: incomeExpenseData.income,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#10B981'
                },
                {
                    label: 'รายจ่าย',
                    data: incomeExpenseData.expense,
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#EF4444'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ฿ ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return '฿ ' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: '#F3F4F6'
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Expense Category Chart
    new Chart(document.getElementById('expenseCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: expenseCategoryData.labels,
            datasets: [{
                data: expenseCategoryData.data,
                backgroundColor: expenseCategoryData.colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = expenseCategoryData.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ฿ ${context.parsed.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Legend
    const legend = document.getElementById('expenseLegend');
    const total = expenseCategoryData.data.reduce((a, b) => a + b, 0) || 1;

    expenseCategoryData.labels.forEach((label, i) => {
        const percentage = ((expenseCategoryData.data[i] / total) * 100).toFixed(1);
        legend.innerHTML += `
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center">
                    <span class="w-2 h-2 rounded-full mr-2" style="background: ${expenseCategoryData.colors[i]}"></span>
                    <span class="text-gray-600">${label}</span>
                </div>
                <span class="font-medium text-gray-900">${percentage}%</span>
            </div>
        `;
    });
});
</script>
@endpush

@push('styles')
<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
    }
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }
</style>
@endpush

@endsection
