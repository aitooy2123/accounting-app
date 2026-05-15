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
        $request->validate([
            'expense_date' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        $docNo = 'EXP-' . date('YmdHis');

        Expense::create([

            'doc_no' => $docNo,

            'expense_date' => $request->expense_date,

            'payee_id' => $request->payee_id,

            'account_id' => $request->account_id,

            'amount' => $request->amount,

            'description' => $request->description,

            'remark' => $request->remark,

            'status' => 'บันทึก',

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

    public function update(Request $request, Expense $expense)
    {
        $expense->update([

            'expense_date' => $request->expense_date,

            'payee_id' => $request->payee_id,

            'account_id' => $request->account_id,

            'amount' => $request->amount,

            'description' => $request->description,

            'remark' => $request->remark,

        ]);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'แก้ไขสำเร็จ');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
