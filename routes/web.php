<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\SalesController; // <--- เช็คว่ามีบรรทัดนี้ไหม
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
    Route::get('/sales', [AccountingController::class, 'sales'])->name('pages.sales');
    Route::get('/sales/create', [AccountingController::class, 'sales_create'])->name('pages.sales_create');
    Route::post('/sales', [AccountingController::class, 'sales_store'])->name('pages.sales_store');
    Route::get('/sales/{id}/edit', [AccountingController::class, 'sales_edit'])->name('pages.sales_edit');
    Route::put('/sales/{id}', [AccountingController::class, 'sales_update'])->name('pages.sales_update');
    Route::delete('/sales/{id}', [AccountingController::class, 'sales_destroy'])->name('pages.sales_destroy');

    Route::get('/sales/{id}/pdf', [AccountingController::class, 'sales_pdf'])->name('pages.sales_pdf');
    Route::get('/sales/export', [SalesController::class, 'exportExcel'])->name('pages.sales_export');
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
});

// ดึง Route สำหรับระบบ Auth (Login, Register, Forgot Password) ที่ Breeze สร้างให้
require __DIR__ . '/auth.php';
