<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ChartOfAccount as Account;

class AccountingController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        return view('dashboard', compact('title'));
    }

    public function banks()
    {
        $bankAccounts = [
            (object)[
                'bank_name' => 'Kasikorn Bank',
                'account_number' => '123-4-56789-0',
                'account_type' => 'ออมทรัพย์',
                'balance' => 1200500,
                'color_class' => 'bg-gradient-to-br from-green-600 to-green-700'
            ],
            (object)[
                'bank_name' => 'Siam Commercial Bank',
                'account_number' => '987-6-54321-0',
                'account_type' => 'กระแสรายวัน',
                'balance' => 250000,
                'color_class' => 'bg-gradient-to-br from-purple-700 to-purple-800'
            ]
        ];

        return view('pages.banks', compact('bankAccounts'));
    }

    /**
     * Display a listing of the sales. (หน้าแสดงรายการขาย)
     */

    /**
     * Remove the specified sale from storage. (ฟังก์ชันลบข้อมูล)
     */

    // เมนูอื่นๆ
    public function customers()
    {
        return view('pages.customers');
    }
    public function company()
    {
        return view('pages.company');
    }
    public function branches()
    {
        return view('pages.branches');
    }

     public function toggleStatus(Account $account, Request $request)
    {
        try {
            $account->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตสถานะเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตสถานะได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete accounts
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:accounts,id'
        ], [
            'ids.required' => 'กรุณาเลือกบัญชีอย่างน้อย 1 รายการ',
            'ids.array' => 'รูปแบบข้อมูลไม่ถูกต้อง',
            'ids.min' => 'กรุณาเลือกบัญชีอย่างน้อย 1 รายการ',
            'ids.*.exists' => 'ไม่พบบัญชีที่เลือกในระบบ'
        ]);

        try {
            DB::beginTransaction();

            $accounts = Account::whereIn('id', $request->ids)->get();
            $count = $accounts->count();

            // Check for child accounts
            foreach ($accounts as $account) {
                if ($account->is_group && $account->children()->exists()) {
                    throw new \Exception("ไม่สามารถลบบัญชีคุม '{$account->code}' ได้ เนื่องจากมีบัญชีย่อยอยู่ภายใต้");
                }

                // Optional: Check if account has transactions
                // if ($account->journalEntries()->exists()) {
                //     throw new \Exception("บัญชี '{$account->code}' มีรายการบัญชีอยู่ ไม่สามารถลบได้");
                // }
            }

            // Delete accounts
            Account::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "ลบบัญชี {$count} รายการเรียบร้อยแล้ว",
                'deleted_count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }
}
