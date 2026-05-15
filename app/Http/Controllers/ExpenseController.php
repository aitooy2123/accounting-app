<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseItem;
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

       $expenses = $query->orderBy('amount', 'asc')->paginate(20);
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
            'doc_no'       => 'nullable|string|max:50|unique:expenses,doc_no', // เพิ่ม Unique Check
            'payee_id'     => 'nullable|exists:payees,id',
            'account_id'   => 'nullable|exists:chart_of_accounts,id',
            'description'  => 'required|string',
            'remark'       => 'nullable|string',
            'status'       => 'required|in:paid,pending,invoiced',
            'vat_rate'     => 'required|numeric|in:0,7,10',
            'amount'       => 'required|numeric|min:0',
            'total_amount' => 'nullable|numeric',
            'items'        => 'nullable|array',
            'items.*.desc' => 'required_with:items|string',
            'items.*.qty'  => 'required_with:items|numeric|min:1',
            'items.*.price'=> 'required_with:items|numeric|min:0',
        ]);

        // Logic การจัดการเลขที่เอกสาร
        $docNo = $validated['doc_no'];
        if (empty($docNo)) {
            $year = date('Y', strtotime($validated['expense_date']));
            $prefix = 'EXP-' . $year . '-';

            // ค้นหาเลขล่าสุดที่ใช้ Prefix นี้
            $lastExpense = Expense::where('doc_no', 'like', $prefix . '%')
                ->orderBy('doc_no', 'desc')
                ->first();

            if ($lastExpense && preg_match('/' . $prefix . '(\d+)/', $lastExpense->doc_no, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            $docNo = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }

        // คำนวณยอดเงิน
        if (!empty($validated['items'])) {
            $subtotal = collect($validated['items'])->sum(fn($item) => $item['qty'] * $item['price']);
            $validated['amount'] = $subtotal;
            $validated['total_amount'] = $subtotal + ($subtotal * $validated['vat_rate'] / 100);
        } else {
            $validated['total_amount'] = $validated['total_amount'] ?? ($validated['amount'] + ($validated['amount'] * $validated['vat_rate'] / 100));
        }

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
            return redirect()->route('expenses.index')->with('success', 'บันทึกสำเร็จ เลขที่เอกสาร: ' . $docNo);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

public function edit(Expense $expense)
{
    $payees = Payee::all();
    $accounts = ChartOfAccount::all();
    return view('pages.expenses.edit', compact('expense', 'payees', 'accounts'));
}
public function update(Request $request, $id)
{
    $expense = Expense::findOrFail($id);

    $validated = $request->validate([
        'expense_date' => 'required|date',
        'description'  => 'required|string',
        'amount'       => 'required|numeric|min:0',
        'vat_rate'     => 'required|numeric|in:0,7,10',
        'status'       => 'required|in:paid,pending,invoiced',
        'payee_id'     => 'nullable|exists:payees,id',
        'account_id'   => 'nullable|exists:chart_of_accounts,id',
        'remark'       => 'nullable|string',
    ]);

    // คำนวณยอดสุทธิใหม่เสมอ เพื่อป้องกันค่าจากหน้า Form ส่งมาผิดพลาด
    $amount = (float)$validated['amount'];
    $vatRate = (int)$validated['vat_rate'];
    $total = $amount + ($amount * $vatRate / 100);

    DB::beginTransaction();
    try {
        $expense->update([
            'expense_date' => $validated['expense_date'],
            'payee_id'     => $validated['payee_id'],
            'description'  => $validated['description'],
            'amount'       => $amount,
            'vat_rate'     => $vatRate,
            'total_amount' => $total,
            'status'       => $validated['status'],
            'account_id'   => $validated['account_id'],
            'remark'       => $validated['remark'], // ใช้ค่าจาก validated
        ]);

        DB::commit();
        return redirect()->route('expenses.index')->with('success', 'อัปเดตข้อมูลสำเร็จ');
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
