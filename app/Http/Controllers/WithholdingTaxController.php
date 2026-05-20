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
        $query = WithholdingTax::with('expense.company');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('withholding_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('expense.company', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->whereHas('expense', fn($q) => $q->where('company_id', $request->company_id));
        }

        // Date range
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
        $autoNumber = $this->generateWithholdingNumber(); // สร้างเลขที่อัตโนมัติ

        return view('pages.withholding-tax.create', compact('companies', 'expenses', 'autoNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. รับข้อมูลทั้งหมด
        $data = $request->all();

        // 2. จัดการ withholding_number (ถ้าไม่มีหรือซ้ำ ให้สร้างใหม่)
        if (empty($data['withholding_number']) ||
            WithholdingTax::where('withholding_number', $data['withholding_number'])->exists()) {
            $data['withholding_number'] = $this->generateWithholdingNumber();
        }

        // 3. Validation (เปลี่ยน nullable เป็น required สำหรับฟิลด์จำเป็น)
        $validated = validator($data, [
            'withholding_number'          => 'required|string|max:50|unique:withholding_taxes,withholding_number',
            'expense_id'                  => 'required|exists:expenses,id',
            'date'                        => 'required|date',
            'invoice_number'              => 'nullable|string|max:100',
            'amount_before_withholding'   => 'required|numeric|min:0',
            'withholding_rate'            => 'required|numeric|min:0|max:100',
            'withholding_amount'          => 'required|numeric|min:0',
            'remark'                      => 'nullable|string',
        ])->validate();

        // 4. ตรวจสอบความถูกต้องของภาษี
        $calculatedAmount = $validated['amount_before_withholding'] * $validated['withholding_rate'] / 100;
        if (abs($calculatedAmount - $validated['withholding_amount']) > 0.01) {
            return back()->withErrors(['withholding_amount' => 'จำนวนภาษีไม่ตรงกับยอดก่อนหักและอัตรา กรุณาคำนวณใหม่'])->withInput();
        }

        // 5. บันทึก
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
            'withholding_number'          => [
                'required',
                'string',
                'max:50',
                Rule::unique('withholding_taxes', 'withholding_number')->ignore($withholdingTax->id)
            ],
            'expense_id'                  => 'required|exists:expenses,id',
            'date'                        => 'required|date',
            'invoice_number'              => 'nullable|string|max:100',
            'amount_before_withholding'   => 'required|numeric|min:0',
            'withholding_rate'            => 'required|numeric|min:0|max:100',
            'withholding_amount'          => 'required|numeric|min:0',
            'remark'                      => 'nullable|string',
        ]);

        // ตรวจสอบการคำนวณ
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

    /**
     * Generate auto withholding number format: WT-YYYY-XXXXX
     */
    private function generateWithholdingNumber()
    {
        $year = date('Y');
        $lastRecord = WithholdingTax::whereYear('date', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord && preg_match('/WT-'.$year.'-(\d+)$/', $lastRecord->withholding_number, $matches)) {
            $lastNumber = intval($matches[1]);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $padded = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        return "WT-{$year}-{$padded}";
    }
}
