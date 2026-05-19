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
        $query = WithholdingTax::with('company');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('withholding_no', 'like', "%{$request->search}%")
                  ->orWhere('invoice_no', 'like', "%{$request->search}%")
                  ->orWhereHas('company', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            });
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('withholding_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('withholding_date', '<=', $request->to_date);
        }

        $withholdingTaxes = $query->orderBy('withholding_date', 'desc')->paginate(15);
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
            'withholding_no'   => 'required|string|max:50|unique:withholding_taxes,withholding_no',
            'company_id'       => 'required|exists:companies,id',
            'expense_id'       => 'nullable|exists:expenses,id',
            'withholding_date' => 'required|date',
            'invoice_no'       => 'nullable|string|max:100',
            'tax_base'         => 'required|numeric|min:0',
            'tax_rate'         => 'required|numeric|min:0|max:100',
            'tax_amount'       => 'required|numeric|min:0',
            'remark'           => 'nullable|string',
        ]);

        // Verify tax_amount matches base * rate
        $calculatedAmount = $validated['tax_base'] * $validated['tax_rate'] / 100;
        if (abs($calculatedAmount - $validated['tax_amount']) > 0.01) {
            return back()->withErrors(['tax_amount' => 'จำนวนภาษีไม่ตรงกับยอดก่อนหักและอัตรา กรุณาคำนวณใหม่'])->withInput();
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
        $withholdingTax->load('company', 'expense');
        return view('pages.withholding-tax.show', compact('withholdingTax'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WithholdingTax $withholdingTax)
    {
        $companies = Company::orderBy('name')->get();
        $expenses = Expense::with('company')->orderBy('expense_date', 'desc')->get();

        // Fixed view path to match others
        return view('pages.withholding-tax.edit', compact('withholdingTax', 'companies', 'expenses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WithholdingTax $withholdingTax)
    {
        $validated = $request->validate([
            'withholding_no'   => ['required', 'string', 'max:50', Rule::unique('withholding_taxes')->ignore($withholdingTax->id)],
            'company_id'       => 'required|exists:companies,id',
            'expense_id'       => 'nullable|exists:expenses,id',
            'withholding_date' => 'required|date',
            'invoice_no'       => 'nullable|string|max:100',
            'tax_base'         => 'required|numeric|min:0',
            'tax_rate'         => 'required|numeric|min:0|max:100',
            'tax_amount'       => 'required|numeric|min:0',
            'remark'           => 'nullable|string',
        ]);

        $calculatedAmount = $validated['tax_base'] * $validated['tax_rate'] / 100;
        if (abs($calculatedAmount - $validated['tax_amount']) > 0.01) {
            return back()->withErrors(['tax_amount' => 'จำนวนภาษีไม่ตรงกับยอดก่อนหักและอัตรา'])->withInput();
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




public function bulkDelete(Request $request)
{
    $ids = $request->input('ids');
    if (empty($ids) || !is_array($ids)) {
        return response()->json(['success' => false, 'message' => 'ไม่มีรายการที่เลือก'], 400);
    }

    try {
        $deleted = WithholdingTax::whereIn('id', $ids)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "ลบเอกสาร {$deleted} รายการเรียบร้อยแล้ว"]);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบรายการที่ต้องการลบ'], 404);
    } catch (\Illuminate\Database\QueryException $e) {
        // ตรวจสอบรหัส error 1451 (foreign key constraint)
        if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถลบได้เนื่องจากมีข้อมูลที่เชื่อมโยงอยู่ (เช่น รายการค่าใช้จ่ายที่ใช้อ้างอิง)'
            ], 409);
        }
        return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()], 500);
    }
}
}
