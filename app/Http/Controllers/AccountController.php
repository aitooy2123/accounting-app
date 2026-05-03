<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount as Account;
use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;


class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = ChartOfAccount::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_th', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $accounts = $query->orderBy('code', 'asc')->paginate(15);

        return view('pages.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ดึงเฉพาะบัญชีที่เป็น "บัญชีคุม" (is_group = true) เพื่อเอาไปให้เลือกเป็น Parent
        $parentAccounts = ChartOfAccount::where('is_group', true)
            ->orderBy('code', 'asc')
            ->get();

        return view('pages.accounts.create', compact('parentAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'      => 'required|unique:chart_of_accounts,code',
            'name_th'   => 'required|string|max:255',
            'category'  => 'required|in:asset,liability,equity,revenue,expense',
            'is_group'  => 'required|boolean',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
        ], [
            'code.unique' => 'รหัสบัญชีนี้มีอยู่ในระบบแล้ว',
            'name_th.required' => 'กรุณากรอกชื่อบัญชีภาษาไทย',
        ]);

        ChartOfAccount::create([
            'code'      => $request->code,
            'name_th'   => $request->name_th,
            'name_en'   => $request->name_en, // รองรับชื่อภาษาอังกฤษ
            'category'  => $request->category,
            'parent_id' => $request->parent_id,
            'is_group'  => $request->is_group,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'บันทึกรหัสบัญชีเรียบร้อยแล้ว');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChartOfAccount $account)
    {
        // ดึงบัญชีที่เป็นกลุ่ม เพื่อให้เลือกเป็นแม่ได้ (และไม่ดึงตัวเองมาเป็นแม่ตัวเอง)
        $parentAccounts = ChartOfAccount::where('is_group', true)
            ->where('id', '!=', $account->id)
            ->orderBy('code', 'asc')
            ->get();

        return view('pages.accounts.edit', compact('account', 'parentAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChartOfAccount $account)
    {
        $request->validate([
            'code'      => 'required|unique:chart_of_accounts,code,' . $account->id,
            'name_th'   => 'required|string|max:255',
            'category'  => 'required|in:asset,liability,equity,revenue,expense',
            'is_group'  => 'required|boolean',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $account->update([
            'code'      => $request->code,
            'name_th'   => $request->name_th,
            'name_en'   => $request->name_en,
            'category'  => $request->category,
            'parent_id' => $request->parent_id,
            'is_group'  => $request->is_group,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'อัปเดตข้อมูลบัญชีเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChartOfAccount $account)
    {
        // 1. ตรวจสอบว่ามีบัญชีลูก (Sub-accounts) หรือไม่
        $hasChildren = ChartOfAccount::where('parent_id', $account->id)->exists();
        if ($hasChildren) {
            return back()->with('error', 'ไม่สามารถลบได้ เนื่องจากมีบัญชีย่อยผูกอยู่');
        }

        // 2. (สำคัญ) ตรวจสอบว่ามีการนำไปใช้ในรายการรายวัน (JournalEntries) หรือยัง
        // $hasTransactions = $account->journalEntries()->exists();
        // if ($hasTransactions) { return back()->with('error', 'ไม่สามารถลบได้เนื่องจากมีการใช้งานในระบบบัญชีแล้ว'); }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'ลบรายการบัญชีเรียบร้อยแล้ว');
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
            'ids.*' => 'required|integer|exists:chart_of_accounts,id'
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
