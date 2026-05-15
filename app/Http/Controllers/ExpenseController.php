<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payee;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

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

        return view('pages.expenses.create', compact(
            'accounts',
            'payees'
        ));
    }

    public function store(Request $request)
    {
        // 1. เพิ่ม Validation สำหรับ status และ vat_rate
        $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'status' => 'required|in:paid,pending,invoiced', // ตรวจสอบค่าสถานะ
            'vat_rate' => 'required|numeric|in:0,7,10',     // ตรวจสอบค่า VAT
        ]);

        $docNo = 'EXP-' . date('YmdHis');

        Expense::create([
            'doc_no' => $docNo,
            'expense_date' => $request->expense_date,
            'payee_id' => $request->payee_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'vat_rate' => $request->vat_rate,      // บันทึก VAT Rate
            'description' => $request->description,
            'remark' => $request->remark,
            'status' => $request->status,          // บันทึกสถานะจาก Form (paid, pending, invoiced)
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'บันทึกค่าใช้จ่ายสำเร็จ');
    }

    public function edit(Expense $expense)
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $payees = Payee::orderBy('name')->get();

        return view('pages.expenses.edit', compact(
            'expense',
            'accounts',
            'payees'
        ));
    }

public function update(Request $request, $id)
{
    $expense = Expense::findOrFail($id);

    $validated = $request->validate([
        'expense_date' => 'required|date',
        'description'  => 'required|string',
        'amount'       => 'required|numeric|min:0',
        'vat_rate'     => 'required|numeric|in:0,7,10',
        'total_amount' => 'nullable|numeric',   // <-- เพิ่มบรรทัดนี้
        'status'       => 'required|in:paid,pending,invoiced',
        'payee_id'     => 'nullable|exists:payees,id',
        'account_id'   => 'nullable|exists:chart_of_accounts,id',
        'remark'       => 'nullable|string',
    ]);

    // ถ้าไม่ส่ง total_amount มาหรือต้องการคำนวณใหม่
    $total = $validated['total_amount'] ?? ($validated['amount'] + ($validated['amount'] * $validated['vat_rate'] / 100));

    $expense->update([
        'expense_date' => $validated['expense_date'],
        'payee_id'     => $validated['payee_id'],
        'description'  => $validated['description'],
        'amount'       => $validated['amount'],
        'vat_rate'     => $validated['vat_rate'],
        'total_amount' => $total,   // <-- บันทึก total_amount
        'status'       => $validated['status'],
        'account_id'   => $validated['account_id'],
        'remark'       => $validated['remark'],
    ]);

    return redirect()->route('expenses.index')->with('success', 'อัปเดตรายการเรียบร้อย');
}
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
