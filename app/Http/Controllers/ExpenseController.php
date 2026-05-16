<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Company;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['company', 'account']);

        // ค้นหา
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doc_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        // กรองตามบริษัท
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        $companies = Company::orderBy('name')->get();

        return view('pages.expenses.index', compact('expenses', 'companies'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $accounts = ChartOfAccount::orderBy('code')->get();
        return view('pages.expenses.create', compact('accounts', 'companies'));
    }
    public function show(Expense $expense)
{
    // ดึงข้อมูล Relation ที่จำเป็น (company, account, items)
    $expense->load(['company', 'account', 'items']);

    return view('pages.expenses.show', compact('expense'));
}

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        // // แปลงค่าว่างของ company_id เป็น null
        // if ($request->input('company_id') === '') {
        //     $request->merge(['company_id' => null]);
        // }

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'doc_no'       => 'nullable|string|max:50|unique:expenses,doc_no',
            'company_id'   => 'required|exists:companies,id',
            'account_id'   => 'nullable|exists:chart_of_accounts,id',
            'description'  => 'nullable|string',
            'remark'       => 'nullable|string',
            'status'       => 'required|in:paid,pending,invoiced',
            'vat_rate'     => 'required|numeric|in:0,7,10',
            'amount'       => 'required_if:items,null|numeric|min:0',
            'total'        => 'nullable|numeric',
            'items'        => 'nullable|array',
            'items.*.desc' => 'required_with:items|string',
            'items.*.qty'  => 'required_with:items|numeric|min:1',
            'items.*.price'=> 'required_with:items|numeric|min:0',
        ]);

        $docNo = $this->generateDocNumber($validated['doc_no'] ?? null, $validated['expense_date']);
        $amount = $this->calculateSubtotal($validated);
        $vatRate = (float) $validated['vat_rate'];
        $total = $amount + ($amount * $vatRate / 100);  // เปลี่ยนชื่อตัวแปรเป็น $total

        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'doc_no'        => $docNo,
                'expense_date'  => $validated['expense_date'],
                'company_id'    => $validated['company_id'],
                'account_id'    => $validated['account_id'] ?? null,
                'description'   => $validated['description'] ?? null,
                'remark'        => $validated['remark'] ?? null,
                'status'        => $validated['status'],
                'amount'        => $amount,
                'vat_rate'      => $vatRate,
                'total'         => $total,  // แก้ไขจาก 'total_amount' เป็น 'total'
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
            return redirect()->route('expenses.index')
                ->with('success', "บันทึกสำเร็จ เลขที่เอกสาร: {$docNo}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
          $expense->load(['items', 'company']);  // เพิ่ม 'company'
        $companies = Company::orderBy('name')->get();
        $accounts = ChartOfAccount::orderBy('code')->get();
        return view('pages.expenses.edit', compact('expense', 'companies', 'accounts'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::with('items')->findOrFail($id);



        $validated = $request->validate([
            'expense_date' => 'required|date',
            'doc_no'       => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('expenses', 'doc_no')
                    ->whereNotNull('doc_no')
                    ->ignore($expense->id),
            ],
            'description'  => 'nullable|string',
            'amount'       => 'nullable|numeric|min:0',
            'vat_rate'     => 'required|numeric|in:0,7,10',
            'status'       => 'required|in:paid,pending,invoiced',
            'company_id'   => 'required|exists:companies,id',
            'account_id'   => 'nullable|exists:chart_of_accounts,id',
            'remark'       => 'nullable|string',
            'items'        => 'nullable|array',
            'items.*.desc' => 'required_with:items|string',
            'items.*.qty'  => 'required_with:items|numeric|min:1',
            'items.*.price'=> 'required_with:items|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $amount = $this->calculateSubtotal($validated);
            $vatRate = (float) $validated['vat_rate'];
            $total = $amount + ($amount * $vatRate / 100);  // เปลี่ยนชื่อตัวแปรเป็น $total

            $docNo = $validated['doc_no'] ?? $expense->doc_no;
            if (empty($docNo)) {
                $docNo = $expense->doc_no;
            }

            $expense->update([
                'doc_no'        => $docNo,
                'expense_date'  => $validated['expense_date'],
                'company_id'    => $validated['company_id'],
                'account_id'    => $validated['account_id'] ?? null,
                'description'   => $validated['description'] ?? null,
                'remark'        => $validated['remark'] ?? null,
                'status'        => $validated['status'],
                'amount'        => $amount,
                'vat_rate'      => $vatRate,
                'total'         => $total,  // แก้ไขจาก 'total_amount' เป็น 'total'
            ]);

            $expense->items()->delete();
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
            return redirect()->route('expenses.index')
                ->with('success', 'อัปเดตข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->items()->delete();
        $expense->delete();
        return back()->with('success', 'ลบข้อมูลสำเร็จ');
    }

    /**
     * Bulk delete expenses.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'ไม่พบรายการที่เลือก']);
        }
        ExpenseItem::whereIn('expense_id', $ids)->delete();
        Expense::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => 'ลบรายการที่เลือกสำเร็จ']);
    }

    /**
     * Generate a unique document number.
     */
    private function generateDocNumber(?string $providedDocNo, string $expenseDate): string
    {
        if (!empty($providedDocNo)) {
            return $providedDocNo;
        }
        $year = date('Y', strtotime($expenseDate));
        $prefix = "EXP-{$year}-";
        $lastExpense = Expense::where('doc_no', 'like', "{$prefix}%")
            ->orderBy('doc_no', 'desc')
            ->first();
        $nextNumber = 1;
        if ($lastExpense && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $lastExpense->doc_no, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }
        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate subtotal from items or fallback to amount field.
     */
    private function calculateSubtotal(array $validated): float
    {
        if (!empty($validated['items'])) {
            return collect($validated['items'])->sum(fn($item) => $item['qty'] * $item['price']);
        }
        return (float) ($validated['amount'] ?? 0);
    }
}
