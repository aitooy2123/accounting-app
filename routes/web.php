<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\SalesController; // <--- เช็คว่ามีบรรทัดนี้ไหม
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;


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
    Route::get('/dashboard', [AccountingController::class, 'index'])->name('dashboard');

    // --- ระบบงานขาย (Sales) ---
    Route::resource('sales', SalesController::class)->except('show');

    Route::get('/sales/{id}/pdf', [SalesController::class, 'pdf'])->name('sales.pdf');
    // Route::get('/sales/export', [SalesController::class, 'exportExcel'])->name('pages.sales_export');

    // --- ระบบงานซื้อ (Purchases) ---
    Route::get('/purchases', function () {
        return view('pages.purchases');
    })->name('purchases');

    // --- สินค้า & สต็อก (Inventory) ---
    Route::get('/inventory', function () {
        return view('pages.inventory');
    })->name('inventory');

    // --- การเงิน & บัญชี (Finance & Accounting) ---
    Route::get('/banks', [AccountingController::class, 'banks'])->name('banks');
    Route::get('/accounting', function () {
        return view('pages.accounting');
    })->name('accounting');

    // --- ตั้งค่าองค์กร (Company Settings) ---
    Route::get('/company', [AccountingController::class, 'company'])->name('company');
    Route::get('/branches', [AccountingController::class, 'branches'])->name('branches');
    Route::get('/customers', [AccountingController::class, 'customers'])->name('customers');


    Route::resource('companies', CompanyController::class);


    Route::resource('branches', BranchController::class);
});

// ดึง Route สำหรับระบบ Auth (Login, Register, Forgot Password) ที่ Breeze สร้างให้
require __DIR__ . '/auth.php';
