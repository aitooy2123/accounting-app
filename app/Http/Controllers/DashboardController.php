<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index(Request $request)
    {
        // กำหนดช่วงวันที่
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        // ============= SUMMARY DATA =============

        // รายรับวันนี้ (จาก invoices + sales)
        $todayIncome = $this->getTodayIncome();

        // รายจ่ายวันนี้ (จาก expenses + purchases)
        $todayExpense = $this->getTodayExpense();

        // กำไรวันนี้
        $todayProfit = $todayIncome - $todayExpense;

        // ข้อมูลค้างชำระ
        $unpaidInvoices = $this->getUnpaidInvoices();
        $unpaidPurchases = $this->getUnpaidPurchases();

        // จำนวนลูกค้า
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // ============= SYSTEM SUMMARY =============

        $systemSummary = [
            'branches' => Branch::count(),
            'companies' => Company::count(),
            'accounts' => ChartOfAccount::count(),
            'active_customers' => $activeCustomers,
            'users' => User::count(),
            'total_invoices' => $this->countTable('invoices'),
            'total_sales' => $this->countTable('sales'),
            'total_purchases' => $this->countTable('purchases'),
            'total_expenses' => $this->countTable('expenses'),
        ];

        // ============= CHART DATA =============

        // กราฟรายรับ-รายจ่าย 7 วัน
        $incomeExpenseChart = $this->getIncomeExpenseChart();

        // สัดส่วนรายจ่ายตามหมวดบัญชี
        $expenseByCategory = $this->getExpenseByCategory();

        // แนวโน้มรายได้ 6 เดือน
        $revenueTrend = $this->getRevenueTrend();

        // ============= RECENT DATA =============

        // รายการขายล่าสุด
        $recentSales = $this->getRecentSales();

        // รายการซื้อล่าสุด
        $recentPurchases = $this->getRecentPurchases();

        // ลูกค้าล่าสุด
        $recentCustomers = $this->getRecentCustomers();

        // ============= TOP DATA =============

        // Top ลูกค้า
        $topCustomers = $this->getTopCustomers($startDate, $endDate);

        return view('dashboard.index', compact(
            'todayIncome',
            'todayExpense',
            'todayProfit',
            'unpaidInvoices',
            'unpaidPurchases',
            'totalCustomers',
            'newCustomersThisMonth',
            'systemSummary',
            'incomeExpenseChart',
            'expenseByCategory',
            'revenueTrend',
            'recentSales',
            'recentPurchases',
            'recentCustomers',
            'topCustomers',
            'startDate',
            'endDate'
        ));
    }

    // ==================== INCOME METHODS ====================

    /**
     * Get today's total income from invoices and sales.
     */
    private function getTodayIncome(): float
    {
        $today = Carbon::today();
        $income = 0;

        // จาก invoices
        if ($this->tableExists('invoices')) {
            $income += DB::table('invoices')
                ->whereDate('invoice_date', $today)
                ->whereNull('deleted_at')
                ->sum('total') ?? 0;
        }

        // จาก sales
        if ($this->tableExists('sales')) {
            $income += DB::table('sales')
                ->whereDate('doc_date', $today)
                ->where('status', '!=', 'ยกเลิก')
                ->sum('total') ?? 0;
        }

        return $income;
    }

    /**
     * Get today's total expense from expenses and purchases.
     */
    private function getTodayExpense(): float
    {
        $today = Carbon::today();
        $expense = 0;

        // จาก expenses
        if ($this->tableExists('expenses')) {
            $expense += DB::table('expenses')
                ->whereDate('expense_date', $today)
                ->whereNull('deleted_at')
                ->sum('amount') ?? 0;
        }

        // จาก purchases (ที่ชำระแล้ว)
        if ($this->tableExists('purchases')) {
            $expense += DB::table('purchases')
                ->whereDate('doc_date', $today)
                ->whereNull('deleted_at')
                ->sum('total') ?? 0;
        }

        return $expense;
    }

    // ==================== UNPAID METHODS ====================

    /**
     * Get unpaid invoices.
     */
    private function getUnpaidInvoices(): array
    {
        $count = 0;
        $total = 0;

        if ($this->tableExists('invoices')) {
            $result = DB::table('invoices')
                ->where('status', 'ค้างชำระ')
                ->whereNull('deleted_at')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
                ->first();
            $count += $result->count ?? 0;
            $total += $result->total ?? 0;
        }

        if ($this->tableExists('sales')) {
            $result = DB::table('sales')
                ->where('status', 'ค้างชำระ')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
                ->first();
            $count += $result->count ?? 0;
            $total += $result->total ?? 0;
        }

        return ['count' => $count, 'total_amount' => $total];
    }

    /**
     * Get unpaid purchases.
     */
    private function getUnpaidPurchases(): array
    {
        if ($this->tableExists('purchases')) {
            $result = DB::table('purchases')
                ->where('status', 'ค้างชำระ')
                ->whereNull('deleted_at')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
                ->first();

            return [
                'count' => $result->count ?? 0,
                'total_amount' => $result->total ?? 0
            ];
        }

        return ['count' => 0, 'total_amount' => 0];
    }

    // ==================== CHART METHODS ====================

    /**
     * Get income vs expense chart data (7 days).
     */
    private function getIncomeExpenseChart(): array
    {
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');

            // รายรับ
            $dailyIncome = 0;
            if ($this->tableExists('invoices')) {
                $dailyIncome += DB::table('invoices')
                    ->whereDate('invoice_date', $date->format('Y-m-d'))
                    ->whereNull('deleted_at')
                    ->sum('total') ?? 0;
            }
            if ($this->tableExists('sales')) {
                $dailyIncome += DB::table('sales')
                    ->whereDate('doc_date', $date->format('Y-m-d'))
                    ->sum('total') ?? 0;
            }

            // รายจ่าย
            $dailyExpense = 0;
            if ($this->tableExists('expenses')) {
                $dailyExpense += DB::table('expenses')
                    ->whereDate('expense_date', $date->format('Y-m-d'))
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;
            }
            if ($this->tableExists('purchases')) {
                $dailyExpense += DB::table('purchases')
                    ->whereDate('doc_date', $date->format('Y-m-d'))
                    ->whereNull('deleted_at')
                    ->sum('total') ?? 0;
            }

            $incomeData[] = $dailyIncome;
            $expenseData[] = $dailyExpense;
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    /**
     * Get expense by category from chart_of_accounts.
     */
    private function getExpenseByCategory(): array
    {
        $expenseAccounts = ChartOfAccount::where('category', 'expense')
            ->where('is_active', true)
            ->where('is_group', false)
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#3B82F6', '#F97316', '#8B5CF6', '#10B981', '#EC4899', '#F59E0B'];

        foreach ($expenseAccounts->take(6) as $account) {
            $labels[] = $account->name_th;
            $amount = 0;

            if ($this->tableExists('expenses')) {
                $amount = DB::table('expenses')
                    ->where('account_id', $account->id)
                    ->whereMonth('expense_date', Carbon::now()->month)
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;
            }

            $data[] = $amount;
        }

        if (empty($labels)) {
            $labels = ['ไม่มีข้อมูล'];
            $data = [0];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels))
        ];
    }

    /**
     * Get revenue trend for 6 months.
     */
    private function getRevenueTrend(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M y');

            $monthlyRevenue = 0;
            if ($this->tableExists('invoices')) {
                $monthlyRevenue += DB::table('invoices')
                    ->whereMonth('invoice_date', $date->month)
                    ->whereYear('invoice_date', $date->year)
                    ->whereNull('deleted_at')
                    ->sum('total') ?? 0;
            }
            if ($this->tableExists('sales')) {
                $monthlyRevenue += DB::table('sales')
                    ->whereMonth('doc_date', $date->month)
                    ->whereYear('doc_date', $date->year)
                    ->sum('total') ?? 0;
            }

            $data[] = $monthlyRevenue;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    // ==================== RECENT DATA METHODS ====================

    /**
     * Get recent sales (last 5).
     */
    private function getRecentSales()
    {
        $sales = collect();

        if ($this->tableExists('invoices') && $this->tableExists('customers')) {
            $invoices = DB::table('invoices')
                ->join('customers', 'invoices.customer_id', '=', 'customers.id')
                ->select('invoices.doc_no', 'invoices.total', 'invoices.status', 'invoices.invoice_date as doc_date', 'customers.name as customer_name')
                ->whereNull('invoices.deleted_at')
                ->orderByDesc('invoices.created_at')
                ->limit(5)
                ->get();
            $sales = $sales->concat($invoices);
        }

        if ($this->tableExists('sales') && $this->tableExists('customers')) {
            $salesData = DB::table('sales')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->select('sales.doc_no', 'sales.total', 'sales.status', 'sales.doc_date', 'customers.name as customer_name')
                ->orderByDesc('sales.created_at')
                ->limit(5)
                ->get();
            $sales = $sales->concat($salesData);
        }

        return $sales->sortByDesc('doc_date')->take(5);
    }

    /**
     * Get recent purchases (last 5).
     */
    private function getRecentPurchases()
    {
        if ($this->tableExists('purchases') && $this->tableExists('customers')) {
            return DB::table('purchases')
                ->join('customers', 'purchases.supplier_id', '=', 'customers.id')
                ->select('purchases.doc_no', 'purchases.total', 'purchases.status', 'purchases.doc_date', 'customers.name as supplier_name')
                ->whereNull('purchases.deleted_at')
                ->orderByDesc('purchases.created_at')
                ->limit(5)
                ->get();
        }

        return collect([]);
    }

    /**
     * Get recent customers.
     */
    private function getRecentCustomers()
    {
        return Customer::with(['company', 'branch'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * Get top customers by purchase amount.
     */
    private function getTopCustomers($startDate, $endDate): array
    {
        if (!$this->tableExists('sales') || !$this->tableExists('customers')) {
            return [];
        }

        return DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->whereBetween('sales.doc_date', [$startDate, $endDate])
            ->select('customers.code', 'customers.name', DB::raw('SUM(sales.total) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('customers.id', 'customers.code', 'customers.name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->toArray();
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Check if a table exists.
     */
    private function tableExists(string $tableName): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Count records in a table.
     */
    private function countTable(string $tableName): int
    {
        if ($this->tableExists($tableName)) {
            return DB::table($tableName)->count();
        }
        return 0;
    }
}
