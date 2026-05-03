<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\ChartOfAccount as Account;
use App\Models\ChartOfAccount;
use App\Models\Customer;
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

        // รายได้ (จากตาราง sales ถ้ามี)
        $todayIncome = $this->getTodayIncome();

        // รายจ่าย (จากตาราง expenses ถ้ามี)
        $todayExpense = $this->getTodayExpense();

        // กำไร
        $todayProfit = $todayIncome - $todayExpense;

        // ใบแจ้งหนี้ค้างชำระ
        $unpaidInvoices = $this->getUnpaidInvoices();

        // จำนวนลูกค้า
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();

        // ลูกค้าใหม่เดือนนี้
        $newCustomersThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // ============= SYSTEM SUMMARY =============

        $systemSummary = [
            'branches' => Branch::count(),
            'companies' => Company::count(),
            'accounts' => Account::count(),
            'active_customers' => $activeCustomers,
        ];

        // ============= CHART DATA =============

        // กราฟรายรับ-รายจ่าย 7 วัน (ใช้ข้อมูลจำลองถ้าไม่มีตาราง)
        $incomeExpenseChart = $this->getIncomeExpenseChart();

        // สัดส่วนรายจ่ายตามหมวดบัญชี
        $expenseByCategory = $this->getExpenseByCategory();

        // แนวโน้มรายได้ 6 เดือน
        $revenueTrend = $this->getRevenueTrend();

        // ============= RECENT DATA =============

        // รายการขายล่าสุด
        $recentSales = $this->getRecentSales();

        // ลูกค้าล่าสุด
        $recentCustomers = $this->getRecentCustomers();

        // ข้อมูลบริษัท/สาขา
        $companies = Company::withCount(['branches', 'customers'])->get();

        return view('dashboard.index', compact(
            'todayIncome',
            'todayExpense',
            'todayProfit',
            'unpaidInvoices',
            'totalCustomers',
            'newCustomersThisMonth',
            'systemSummary',
            'incomeExpenseChart',
            'expenseByCategory',
            'revenueTrend',
            'recentSales',
            'recentCustomers',
            'companies',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get today's income (check if sales/invoices table exists).
     */
    private function getTodayIncome(): float
    {
        $today = Carbon::today();

        // ตรวจสอบว่ามีตาราง invoices หรือไม่
        if ($this->tableExists('invoices')) {
            return DB::table('invoices')
                ->whereDate('invoice_date', $today)
                ->where('status', '!=', 'void')
                ->sum('total') ?? 0;
        }

        // ถ้ามีตาราง sales แทน
        if ($this->tableExists('sales')) {
            return DB::table('sales')
                ->whereDate('doc_date', $today)
                ->sum('total') ?? 0;
        }

        // ใช้ข้อมูลจำลอง
        return rand(10000, 50000);
    }

    /**
     * Get today's expense.
     */
    private function getTodayExpense(): float
    {
        $today = Carbon::today();

        // ตรวจสอบว่ามีตาราง expenses หรือไม่
        if ($this->tableExists('expenses')) {
            return DB::table('expenses')
                ->whereDate('expense_date', $today)
                ->sum('amount') ?? 0;
        }

        // ใช้ข้อมูลจำลอง
        return rand(5000, 20000);
    }

    /**
     * Get unpaid invoices.
     */
    private function getUnpaidInvoices(): array
    {
        // ตรวจสอบว่ามีตาราง invoices หรือไม่
        if ($this->tableExists('invoices')) {
            $unpaid = DB::table('invoices')
                ->where('status', 'ค้างชำระ')
                ->whereNull('deleted_at')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total_amount')
                ->first();

            return [
                'count' => $unpaid->count ?? 0,
                'total_amount' => $unpaid->total_amount ?? 0
            ];
        }

        if ($this->tableExists('sales')) {
            $unpaid = DB::table('sales')
                ->where('status', 'ค้างชำระ')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total_amount')
                ->first();

            return [
                'count' => $unpaid->count ?? 0,
                'total_amount' => $unpaid->total_amount ?? 0
            ];
        }

        // ข้อมูลจำลอง
        return [
            'count' => rand(3, 10),
            'total_amount' => rand(10000, 50000)
        ];
    }

    /**
     * Get income vs expense chart data.
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
            if ($this->tableExists('invoices')) {
                $dailyIncome = DB::table('invoices')
                    ->whereDate('invoice_date', $date->format('Y-m-d'))
                    ->sum('total') ?? 0;
            } elseif ($this->tableExists('sales')) {
                $dailyIncome = DB::table('sales')
                    ->whereDate('doc_date', $date->format('Y-m-d'))
                    ->sum('total') ?? 0;
            } else {
                $dailyIncome = rand(5000, 30000);
            }

            // รายจ่าย
            if ($this->tableExists('expenses')) {
                $dailyExpense = DB::table('expenses')
                    ->whereDate('expense_date', $date->format('Y-m-d'))
                    ->sum('amount') ?? 0;
            } else {
                $dailyExpense = rand(3000, 15000);
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
     * Get expense by category (from chart_of_accounts).
     */
    private function getExpenseByCategory(): array
    {
        // ใช้ข้อมูลจาก chart_of_accounts หมวด expense
        $expenseAccounts = Account::where('category', 'expense')
            ->where('is_active', true)
            ->where('is_group', false)
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            '#3B82F6', '#F97316', '#8B5CF6', '#10B981',
            '#EC4899', '#F59E0B', '#6366F1', '#14B8A6'
        ];

        foreach ($expenseAccounts->take(6) as $index => $account) {
            $labels[] = $account->name_th;

            // ถ้ามีตาราง expenses ให้ดึงยอดจริง
            if ($this->tableExists('expenses')) {
                $amount = DB::table('expenses')
                    ->where('account_id', $account->id)
                    ->whereMonth('expense_date', Carbon::now()->month)
                    ->sum('amount') ?? 0;
            } else {
                $amount = rand(2000, 20000);
            }

            $data[] = $amount;
        }

        return [
            'labels' => $labels ?: ['ค่าใช้จ่ายทั่วไป'],
            'data' => $data ?: [100],
            'colors' => array_slice($colors, 0, count($labels) ?: 1)
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
            $labels[] = $date->format('M Y');

            if ($this->tableExists('invoices')) {
                $monthlyRevenue = DB::table('invoices')
                    ->whereMonth('invoice_date', $date->month)
                    ->whereYear('invoice_date', $date->year)
                    ->sum('total') ?? 0;
            } elseif ($this->tableExists('sales')) {
                $monthlyRevenue = DB::table('sales')
                    ->whereMonth('doc_date', $date->month)
                    ->whereYear('doc_date', $date->year)
                    ->sum('total') ?? 0;
            } else {
                $monthlyRevenue = rand(50000, 200000);
            }

            $data[] = $monthlyRevenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get recent sales.
     */
    private function getRecentSales()
    {
        // ตรวจสอบว่ามีตาราง invoices หรือ sales
        if ($this->tableExists('invoices')) {
            return DB::table('invoices')
                ->join('customers', 'invoices.customer_id', '=', 'customers.id')
                ->select(
                    'invoices.doc_no',
                    'invoices.total',
                    'invoices.status',
                    'invoices.invoice_date',
                    'customers.name as customer_name'
                )
                ->orderByDesc('invoices.created_at')
                ->limit(5)
                ->get();
        }

        if ($this->tableExists('sales')) {
            return DB::table('sales')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->select(
                    'sales.doc_no',
                    'sales.total',
                    'sales.status',
                    'sales.doc_date as invoice_date',
                    'customers.name as customer_name'
                )
                ->orderByDesc('sales.created_at')
                ->limit(5)
                ->get();
        }

        // Return empty collection
        return collect([]);
    }

    /**
     * Get recent customers.
     */
    private function getRecentCustomers()
    {
        return Customer::with(['company', 'branch'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * Check if a table exists in the database.
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
     * API: Get dashboard summary.
     */
    public function summary(Request $request)
    {
        return response()->json([
            'today_income' => $this->getTodayIncome(),
            'today_expense' => $this->getTodayExpense(),
            'unpaid_invoices' => $this->getUnpaidInvoices(),
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('is_active', true)->count(),
            'new_customers_today' => Customer::whereDate('created_at', Carbon::today())->count(),
        ]);
    }

    /**
     * API: Get chart data.
     */
    public function chartData(Request $request)
    {
        $type = $request->type ?? 'income_expense';

        switch ($type) {
            case 'income_expense':
                $data = $this->getIncomeExpenseChart();
                break;
            case 'expense_category':
                $data = $this->getExpenseByCategory();
                break;
            case 'revenue_trend':
                $data = $this->getRevenueTrend();
                break;
            default:
                $data = $this->getIncomeExpenseChart();
        }

        return response()->json($data);
    }
}
