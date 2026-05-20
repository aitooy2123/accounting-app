<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\SalesController; // <--- เช็คว่ามีบรรทัดนี้ไหม
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Exports\SaleExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ReportController; // Ensure you have this controller
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\WithholdingTaxController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// หน้าแรก (Landing Page) - ถ้ายังไม่ Login ให้ไปหน้า Login ของ Breeze
Route::get('/', function () {
    return view('welcome');
});

// กลุ่ม Route ที่ต้องผ่านการ Login (Auth) เท่านั้น
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Dashboard API for AJAX
    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])
        ->name('dashboard.summary');

    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])
        ->name('dashboard.chart-data');

    // --- ระบบงานขาย (Sales) ---
    Route::resource('sales', SalesController::class)->except('show');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sale.show');

    Route::get('sales/{id}/export', function ($id) {
        $sale = App\Models\Sale::findOrFail($id);
        return Excel::download(new SaleExport($id), 'Invoice-' . $sale->doc_no . '.xlsx');
    })->name('sales.export');

    Route::get('/sales/{id}/pdf', [SalesController::class, 'pdf'])->name('sales.pdf');
    Route::post('/sales/bulk-delete', [SalesController::class, 'bulkDelete'])
        ->name('sales.bulk-delete');
    // Route::get('/sales/export', [SalesController::class, 'exportExcel'])->name('pages.sales_export');
    Route::post('/sales/bulk-delete', [SalesController::class, 'bulkDelete'])->name('sales.bulk-delete');
Route::get('/quotations/showsale/{id}', [SalesController::class, 'showsale'])->name('quotations.showsale');    // --- ระบบงานซื้อ (Purchases) ---
    // Route::get('/purchases', function () {
    //     return view('pages.purchase.index');
    // })->name('purchases');

    // routes/web.php


    // Purchase Resource
    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/toggle-status', [PurchaseController::class, 'toggleStatus'])->name('purchases.toggle-status');
    Route::post('/purchases/bulk-delete', [PurchaseController::class, 'bulkDelete'])->name('purchases.bulk-delete');
    // Restore (Soft Delete)
    Route::post('/purchases/{id}/restore', [PurchaseController::class, 'restore'])
        ->name('purchases.restore');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    // Force Delete
    Route::delete('/purchases/{id}/force-delete', [PurchaseController::class, 'forceDelete'])
        ->name('purchases.force-delete');
    Route::get('/purchases/{id}/export', [PurchaseController::class, 'export'])->name('purchases.export');



    // --- สินค้า & สต็อก (Inventory) ---
    Route::get('/inventory', function () {
        return view('pages.inventory');
    })->name('inventory');

    // --- การเงิน & บัญชี (Finance & Accounting) ---
    Route::get('/banks', [AccountingController::class, 'banks'])->name('banks');
    Route::get('/accounting', function () {
        return view('pages.accounting');
    })->name('accounting');
    // Toggle status route
    Route::post('/accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');

    // Bulk delete route
    Route::post('/accounts/bulk-delete', [AccountController::class, 'bulkDelete'])->name('accounts.bulk-delete');

    // --- ตั้งค่าองค์กร (Company Settings) ---
    Route::get('/company', [AccountingController::class, 'company'])->name('company');
    Route::get('/branches', [AccountingController::class, 'branches'])->name('branches');
    Route::post('/branches/bulk-delete', [BranchController::class, 'bulkDelete'])->name('branches.bulk-delete');
    Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');

    Route::get('/customers', [AccountingController::class, 'customers'])->name('customers');
    // Toggle status route
    Route::post('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])
        ->name('customers.toggle-status');

    // Bulk delete route
    Route::post('/customers/bulk-delete', [CustomerController::class, 'bulkDelete'])
        ->name('customers.bulk-delete');

    Route::resource('companies', CompanyController::class);
    Route::post('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus']);
    Route::post('/companies/bulk-delete', [CompanyController::class, 'bulkDelete'])->name('companies.bulk-delete');

    //ผังบัญชี
    Route::resource('accounts', AccountController::class);
    // Toggle status route
    Route::post('/accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])
        ->name('accounts.toggle-status');

    // Bulk delete route
    Route::post('/accounts/bulk-delete', [AccountController::class, 'bulkDelete'])
        ->name('accounts.bulk-delete');

    // สาขา
    Route::resource('branches', BranchController::class);
    // Bulk Delete สำหรับสาขา
    // Toggle status route
    Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])
        ->name('branches.toggle-status');

    // Bulk delete route
    Route::post('/branches/bulk-delete', [BranchController::class, 'bulkDelete'])
        ->name('branches.bulk-delete');
});


//ลูกค้า

Route::get('/customers/template', [CustomerController::class, 'downloadTemplate'])
    ->name('customers.template');
Route::post('/customers/import', [CustomerController::class, 'import'])
    ->name('customers.import');

Route::resource('customers', CustomerController::class);
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');



Route::prefix('reports')->group(function () {
    Route::get('/journal', [ReportController::class, 'journal'])->name('reports.journal');
    Route::get('/journal/export', [ReportController::class, 'exportJournalExcel'])->name('reports.journal.export');
     Route::get('/customer-statement/{customerId?}', [ReportController::class, 'customerStatement'])->name('reports.customer-statement');
});


//ค่าใช้จ่าย
Route::resource('expenses', ExpenseController::class);
Route::get('expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
 Route::post('/expenses/bulk-delete', [ExpenseController::class, 'bulkDelete'])->name('expenses.bulk-delete');




Route::resource('withholding-tax', WithholdingTaxController::class);
Route::post('/withholding-tax/bulk-delete', [WithholdingTaxController::class, 'bulkDelete'])->name('withholding-tax.bulk-delete');
require __DIR__ . '/auth.php';
