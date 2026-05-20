<?php

namespace App\Http\Controllers;

use App\Models\WithholdingTax;
use App\Models\Company;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class WithholdingTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WithholdingTax::with('expense.company'); // ผ่าน expense ไปหา company

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('withholding_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('expense.company', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by company (ผ่าน expense)
        if ($request->filled('company_id')) {
            $query->whereHas('expense', fn($q) => $q->where('company_id', $request->company_id));
        }

        // Date range (ใช้ฟิลด์ `date`)
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $withholdingTaxes = $query->orderBy('date', 'desc')->paginate(15);
        $companies = Company::orderBy('name')->get();

        return view('pages.withholding-tax.index', compact('withholdingTaxes', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $expenses = Expense::with('company')->orderBy('expense_date', 'desc')->get();

        return view('pages.withholding-tax.create', compact('companies', 'expenses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'withholding_number'          => 'nullable|string|max:50|unique:withholding_taxes,withholding_number',
            'expense_id'                  => 'nullable|exists:expenses,id',
            'date'                        => 'nullable|date',
            'invoice_number'              => 'nullable|string|max:100',
            'amount_before_withholding'   => 'nullable|numeric|min:0',
            'withholding_rate'            => 'nullable|numeric|min:0|max:100',
            'withholding_amount'          => 'nullable|numeric|min:0',
            'remark'                      => 'nullable|string',
        ]);

        // Verify tax_amount matches base * rate
        $calculatedAmount = $validated['amount_before_withholding'] * $validated['withholding_rate'] / 100;
        if (abs($calculatedAmount - $validated['withholding_amount']) > 0.01) {
            return back()->withErrors(['withholding_amount' => 'จำนวนภาษีไม่ตรงกับยอดก่อนหักและอัตรา กรุณาคำนวณใหม่'])->withInput();
        }

        WithholdingTax::create($validated);

        return redirect()->route('withholding-tax.index')
            ->with('success', 'เพิ่มข้อมูลหัก ณ ที่จ่ายเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(WithholdingTax $withholdingTax)
    {
        $withholdingTax->load('expense.company');
        return view('pages.withholding-tax.show', compact('withholdingTax'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WithholdingTax $withholdingTax)
    {
        $companies = Company::orderBy('name')->get();
        $expenses = Expense::with('company')->orderBy('expense_date', 'desc')->get();

        return view('pages.withholding-tax.edit', compact('withholdingTax', 'companies', 'expenses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WithholdingTax $withholdingTax)
    {
        $validated = $request->validate([
            'withholding_number'          => ['nullable', 'string', 'max:50', Rule::unique('withholding_taxes')->ignore($withholdingTax->id)],
            'expense_id'                  => 'nullable|exists:expenses,id',
            'date'                        => 'nullable|date',
            'invoice_number'              => 'nullable|string|max:100',
            'amount_before_withholding'   => 'nullable|numeric|min:0',
            'withholding_rate'            => 'nullable|numeric|min:0|max:100',
            'withholding_amount'          => 'nullable|numeric|min:0',
            'remark'                      => 'nullable|string',
        ]);

        $calculatedAmount = $validated['amount_before_withholding'] * $validated['withholding_rate'] / 100;
        if (abs($calculatedAmount - $validated['withholding_amount']) > 0.01) {
            return back()->withErrors(['withholding_amount' => 'จำนวนภาษีไม่ตรงกับยอดก่อนหักและอัตรา'])->withInput();
        }

        $withholdingTax->update($validated);

        return redirect()->route('withholding-tax.index')
            ->with('success', 'อัปเดตข้อมูลหัก ณ ที่จ่ายเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WithholdingTax $withholdingTax)
    {
        try {
            $withholdingTax->delete();
            return redirect()->route('withholding-tax.index')
                ->with('success', 'ลบรายการหัก ณ ที่จ่ายเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->route('withholding-tax.index')
                ->with('error', 'ไม่สามารถลบได้เนื่องจากมีข้อมูลเชื่อมโยงอยู่');
        }
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        WithholdingTax::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => 'ลบข้อมูลเรียบร้อยแล้ว']);
    }
}
