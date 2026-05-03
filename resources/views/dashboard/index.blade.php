{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">ภาพรวมธุรกิจ (Dashboard)</h1>
            <p class="text-sm text-gray-500 font-kanit">สรุปข้อมูลสำคัญ ณ วันที่ {{ now()->format('d/m/Y') }} เวลา {{ now()->format('H:i') }} น.</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            {{-- ช่วงวันที่ --}}
            <form id="dateRangeForm" method="GET" class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-kanit focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400">ถึง</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-kanit focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-kanit hover:bg-blue-700">
                    <i class="fas fa-filter mr-1"></i>กรอง
                </button>
            </form>
            <button class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-kanit hover:bg-gray-50" onclick="window.print()">
                <i class="fas fa-print mr-2"></i>พิมพ์รายงาน
            </button>
        </div>
    </div>

    {{-- ROW 1: SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Card: รายรับทั้งหมด --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-kanit">รายรับทั้งหมด</p>
                    <p class="text-2xl font-bold text-green-600 font-kanit mt-1">
                        ฿ {{ number_format($todayIncome ?? 0, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-trend-up text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 font-kanit">ข้อมูลวันนี้</p>
        </div>

        {{-- Card: รายจ่ายทั้งหมด --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-kanit">รายจ่ายทั้งหมด</p>
                    <p class="text-2xl font-bold text-red-500 font-kanit mt-1">
                        ฿ {{ number_format($todayExpense ?? 0, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-trend-down text-red-500 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 font-kanit">ข้อมูลวันนี้</p>
        </div>

        {{-- Card: กำไรสุทธิ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-kanit">กำไรสุทธิ</p>
                    <p class="text-2xl font-bold text-blue-600 font-kanit mt-1">
                        ฿ {{ number_format($todayProfit ?? 0, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 font-kanit">
                {{ ($todayIncome > 0) ? round(($todayProfit / $todayIncome) * 100, 1) : 0 }}% Margin
            </p>
        </div>

        {{-- Card: ใบแจ้งหนี้ค้างชำระ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-kanit">ค้างชำระ</p>
                    <p class="text-2xl font-bold text-orange-500 font-kanit mt-1">
                        {{ $unpaidInvoices['count'] ?? 0 }} ใบ
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-orange-500 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-orange-500 mt-2 font-kanit">
                มูลค่ารวม ฿ {{ number_format($unpaidInvoices['total_amount'] ?? 0, 2) }}
            </p>
        </div>
    </div>

    {{-- ROW 2: MINI CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
            <i class="fas fa-users text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $totalCustomers ?? 0 }}</p>
            <p class="text-sm opacity-90 font-kanit">ลูกค้าทั้งหมด</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <i class="fas fa-user-plus text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $newCustomersThisMonth ?? 0 }}</p>
            <p class="text-sm opacity-90 font-kanit">ลูกค้าใหม่เดือนนี้</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
            <i class="fas fa-building text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $systemSummary['branches'] ?? 0 }}</p>
            <p class="text-sm opacity-90 font-kanit">สาขาทั้งหมด</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-4 text-white">
            <i class="fas fa-file-invoice-dollar text-2xl mb-2 opacity-80"></i>
            <p class="text-2xl font-bold">{{ $systemSummary['accounts'] ?? 0 }}</p>
            <p class="text-sm opacity-90 font-kanit">รหัสบัญชี</p>
        </div>
    </div>

    {{-- ROW 3: CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
        {{-- กราฟเส้น: รายรับ vs รายจ่าย 7 วัน --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-700 font-kanit mb-4">
                <i class="fas fa-chart-line mr-2 text-blue-500"></i>รายรับ - รายจ่าย (7 วันล่าสุด)
            </h2>
            <canvas id="incomeExpenseChart" height="120"></canvas>
        </div>

        {{-- Donut Chart: สัดส่วนรายจ่าย --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-700 font-kanit mb-4">
                <i class="fas fa-chart-pie mr-2 text-purple-500"></i>สัดส่วนรายจ่าย
            </h2>
            <div class="relative" style="max-width: 200px; margin: 0 auto;">
                <canvas id="expenseCategoryChart"></canvas>
            </div>
            <div class="mt-4 space-y-2 text-xs font-kanit" id="expenseLegend"></div>
        </div>
    </div>

    {{-- ROW 4: กราฟแนวโน้มรายได้ 6 เดือน --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
        <h2 class="text-sm font-bold text-gray-700 font-kanit mb-4">
            <i class="fas fa-chart-bar mr-2 text-green-500"></i>แนวโน้มรายได้ย้อนหลัง 6 เดือน
        </h2>
        <canvas id="revenueTrendChart" height="80"></canvas>
    </div>

    {{-- ROW 5: TABLES --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- รายการขายล่าสุด --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-sm font-bold text-gray-700 font-kanit">
                    <i class="fas fa-receipt mr-2 text-blue-500"></i>รายการขายล่าสุด
                </h2>
                <a href="{{ route('sales.index') }}" class="text-xs text-blue-600 font-kanit hover:underline">ดูทั้งหมด</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left font-kanit text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs text-gray-500">เลขที่เอกสาร</th>
                            <th class="px-4 py-3 text-xs text-gray-500">ลูกค้า</th>
                            <th class="px-4 py-3 text-xs text-gray-500 text-right">จำนวนเงิน</th>
                            <th class="px-4 py-3 text-xs text-gray-500 text-center">สถานะ</th>
                            <th class="px-4 py-3 text-xs text-gray-500">วันที่</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentSales ?? [] as $sale)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-blue-600">{{ $sale->doc_no }}</td>
                            <td class="px-4 py-3">{{ $sale->customer_name }}</td>
                            <td class="px-4 py-3 text-right font-bold">฿ {{ number_format($sale->total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $sale->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $sale->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sale->invoice_date)->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 font-kanit">ไม่มีรายการขาย</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ลูกค้าที่มียอดซื้อสูงสุด --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-sm font-bold text-gray-700 font-kanit">
                    <i class="fas fa-star mr-2 text-yellow-500"></i>Top ลูกค้า
                </h2>
                <a href="{{ route('customers.index') }}" class="text-xs text-blue-600 font-kanit hover:underline">ดูทั้งหมด</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left font-kanit text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs text-gray-500">#</th>
                            <th class="px-4 py-3 text-xs text-gray-500">ลูกค้า</th>
                            <th class="px-4 py-3 text-xs text-gray-500 text-right">ยอดซื้อ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topCustomers ?? [] as $index => $customer)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-4 py-3">
                                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $customer['name'] }}</div>
                                <div class="text-xs text-gray-400">{{ $customer['code'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-bold">
                                ฿ {{ number_format($customer['total_amount'], 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400 font-kanit">ไม่มีข้อมูล</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ROW 6: สินค้าขายดี --}}
    <div class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-sm font-bold text-gray-700 font-kanit">
                <i class="fas fa-box mr-2 text-green-500"></i>สินค้าขายดี (Top 5)
            </h2>
            <a href="#" class="text-xs text-blue-600 font-kanit hover:underline">ดูทั้งหมด</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left font-kanit text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs text-gray-500">#</th>
                        <th class="px-4 py-3 text-xs text-gray-500">ชื่อสินค้า</th>
                        <th class="px-4 py-3 text-xs text-gray-500 text-right">จำนวนที่ขาย</th>
                        <th class="px-4 py-3 text-xs text-gray-500 text-right">มูลค่ารวม</th>
                        <th class="px-4 py-3 text-xs text-gray-500">สัดส่วน</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $totalAmount = array_sum(array_column($topProducts ?? [], 'total_amount'));
                    @endphp
                    @forelse($topProducts ?? [] as $index => $product)
                    @php
                        $percentage = $totalAmount > 0 ? ($product['total_amount'] / $totalAmount) * 100 : 0;
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-3 font-bold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium">{{ $product['name'] }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product['total_quantity']) }}</td>
                        <td class="px-4 py-3 text-right font-bold">฿ {{ number_format($product['total_amount'], 2) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ round($percentage, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400 font-kanit">ไม่มีข้อมูลสินค้า</td>
                    </tr>
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
        // ========================================
        // CHART 1: รายรับ vs รายจ่าย 7 วัน
        // ========================================
        const incomeExpenseData = @json($incomeExpenseChart);
        const ctx1 = document.getElementById('incomeExpenseChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: incomeExpenseData.labels,
                datasets: [
                    {
                        label: 'รายรับ',
                        data: incomeExpenseData.income,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'รายจ่าย',
                        data: incomeExpenseData.expense,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointBackgroundColor: '#EF4444',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Kanit', size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ฿ ' + Number(context.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '฿ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // ========================================
        // CHART 2: สัดส่วนรายจ่าย (Donut)
        // ========================================
        const expenseCategoryData = @json($expenseByCategory);
        const ctx2 = document.getElementById('expenseCategoryChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: expenseCategoryData.labels,
                datasets: [{
                    data: expenseCategoryData.data,
                    backgroundColor: expenseCategoryData.colors,
                    borderWidth: 4,
                    borderColor: '#FFFFFF',
                    hoverBorderWidth: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return context.label + ': ฿ ' + Number(context.raw).toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Generate expense legend dynamically
        const legendContainer = document.getElementById('expenseLegend');
        expenseCategoryData.labels.forEach((label, index) => {
            const total = expenseCategoryData.data.reduce((a, b) => a + b, 0);
            const percentage = ((expenseCategoryData.data[index] / total) * 100).toFixed(1);
            const color = expenseCategoryData.colors[index];

            const div = document.createElement('div');
            div.className = 'flex justify-between';
            div.innerHTML = `
                <span class="text-gray-500">
                    <span class="w-3 h-3 inline-block rounded-full mr-1" style="background-color: ${color}"></span>${label}
                </span>
                <span class="font-bold">${percentage}%</span>
            `;
            legendContainer.appendChild(div);
        });

        // ========================================
        // CHART 3: แนวโน้มรายได้ 6 เดือน
        // ========================================
        const revenueTrendData = @json($revenueTrend);
        const ctx3 = document.getElementById('revenueTrendChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: revenueTrendData.labels,
                datasets: [{
                    label: 'รายได้',
                    data: revenueTrendData.data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.8)'
                    ],
                    borderColor: [
                        '#3B82F6',
                        '#3B82F6',
                        '#3B82F6',
                        '#3B82F6',
                        '#3B82F6',
                        '#10B981'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'รายได้: ฿ ' + Number(context.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '฿ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
