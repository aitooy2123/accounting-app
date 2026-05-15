<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseItem; // ต้องมี Model นี้
use App\Models\Payee;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['payee', 'account']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('doc_no', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $expenses = $query->latest()->paginate(20);

        return view('pages.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $payees = Payee::orderBy('name')->get();

        return view('pages.expenses.create', compact('accounts', 'payees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'payee_id'     => 'nullable|exists:payees,id',
            'account_id'   => 'nullable|exists:chart_of_accounts,id',
            'description'  => 'required|string',
            'remark'       => 'nullable|string',
            'status'       => 'required|in:paid,pending,invoiced',
            'vat_rate'     => 'required|numeric|in:0,7,10',
            'amount'       => 'required|numeric|min:0',        // subtotal จากฟอร์ม
            'total_amount' => 'nullable|numeric',              // grand total ที่ส่งมาจากฟอร์ม
            'items'        => 'nullable|array',
            'items.*.desc' => 'required_with:items|string',
            'items.*.qty'  => 'required_with:items|numeric|min:1',
            'items.*.price'=> 'required_with:items|numeric|min:0',
        ]);

        // ถ้ามีรายการย่อย ให้คำนวณ amount และ total_amount ใหม่ (เผื่อป้องกันข้อมูลไม่ตรง)
        if (!empty($validated['items'])) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['qty'] * $item['price'];
            }
            $validated['amount'] = $subtotal;
            $validated['total_amount'] = $subtotal + ($subtotal * $validated['vat_rate'] / 100);
        } else {
            // ถ้าไม่มี items ให้ใช้ amount และ total_amount ที่ส่งมา หรือคำนวณ total_amount
            $validated['total_amount'] = $validated['total_amount'] ?? ($validated['amount'] + ($validated['amount'] * $validated['vat_rate'] / 100));
        }

        // สร้างเลขที่เอกสารแบบไม่ซ้ำ (ใช้ปี+ลำดับ)
        $year = date('Y');
        $lastExpense = Expense::whereYear('expense_date', $year)->orderBy('id', 'desc')->first();
        if ($lastExpense && preg_match('/EXP-' . $year . '-(\d+)/', $lastExpense->doc_no, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $docNo = 'EXP-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'doc_no'        => $docNo,
                'expense_date'  => $validated['expense_date'],
                'payee_id'      => $validated['payee_id'],
                'account_id'    => $validated['account_id'],
                'amount'        => $validated['amount'],
                'vat_rate'      => $validated['vat_rate'],
                'total_amount'  => $validated['total_amount'],
                'description'   => $validated['description'],
                'remark'        => $validated['remark'],
                'status'        => $validated['status'],
                'created_by'    => auth()->id(),
            ]);

            // บันทึกรายการย่อย
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    $expense->items()->create([
                        'description' => $item['desc'],
                        'quantity'    => $item['qty'],
                        'unit_price'  => $item['price'],
                        'total'       => $item['qty'] * $item['price'],
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('expenses.index')
                ->with('success', 'บันทึกค่าใช้จ่ายสำเร็จ เลขที่เอกสาร: ' . $docNo);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function edit(Expense $expense)
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $payees = Payee::orderBy('name')->get();

        return view('pages.expenses.edit', compact('expense', 'accounts', 'payees'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'description'  => 'required|string',
            'amount'       => 'required|numeric|min:0',
            'vat_rate'     => 'required|numeric|in:0,7,10',
            'total_amount' => 'nullable|numeric',
            'status'       => 'required|in:paid,pending,invoiced',
            'payee_id'     => 'nullable|exists:payees,id',
            'account_id'   => 'nullable|exists:chart_of_accounts,id',
            'remark'       => 'nullable|string',
            'items'        => 'nullable|array',      // ถ้ามีการแก้ไขรายการย่อย
        ]);

        // คำนวณ total_amount ถ้าไม่ได้ส่งมา
        $total = $validated['total_amount'] ?? ($validated['amount'] + ($validated['amount'] * $validated['vat_rate'] / 100));

        DB::beginTransaction();
        try {
            $expense->update([
                'expense_date' => $validated['expense_date'],
                'payee_id'     => $validated['payee_id'],
                'description'  => $validated['description'],
                'amount'       => $validated['amount'],
                'vat_rate'     => $validated['vat_rate'],
                'total_amount' => $total,
                'status'       => $validated['status'],
                'account_id'   => $validated['account_id'],
                'remark'       => $validated['remark'],
            ]);

            // อัปเดตรายการย่อย (ถ้ามีการส่ง items มา จะลบของเก่าแล้วสร้างใหม่)
            if (isset($validated['items'])) {
                $expense->items()->delete();
                foreach ($validated['items'] as $item) {
                    $expense->items()->create([
                        'description' => $item['desc'],
                        'quantity'    => $item['qty'],
                        'unit_price'  => $item['price'],
                        'total'       => $item['qty'] * $item['price'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('expenses.index')->with('success', 'อัปเดตรายการเรียบร้อย');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
