<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases.
     */
    public function index(Request $request)
    {
        $query = Purchase::query()
            ->with(['supplier', 'branch']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->filled('start_date')) {
            $query->whereDate('doc_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('doc_date', '<=', $request->end_date);
        }

        $purchases = $query->latest('doc_date')->paginate(15);

        return view('pages.purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $suppliers = Customer::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();
        $autoDocNo = Purchase::generateDocNo();

        return view('pages.purchase.create', compact('suppliers', 'branches', 'autoDocNo'));
    }

    /**
     * Store a newly created purchase.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doc_no' => 'required|string|unique:purchases,doc_no',
            'supplier_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'doc_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:doc_date',
            'subtotal' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'note' => 'nullable|string|max:1000',
        ], [
            'doc_no.unique' => 'เลขที่เอกสารนี้มีอยู่ในระบบแล้ว',
            'supplier_id.required' => 'กรุณาเลือกเจ้าหนี้/ผู้ขาย',
            'branch_id.required' => 'กรุณาเลือกสาขา',
            'doc_date.required' => 'กรุณาระบุวันที่เอกสาร',
            'due_date.required' => 'กรุณาระบุวันที่ครบกำหนด',
            'due_date.after_or_equal' => 'วันที่ครบกำหนดต้องไม่ก่อนวันที่เอกสาร',
        ]);

        // Calculate VAT and Total
        $validated['vat'] = round($validated['subtotal'] * ($validated['vat_rate'] / 100), 2);
        $validated['total'] = $validated['subtotal'] + $validated['vat'];
        $validated['status'] = 'ค้างชำระ';

        Purchase::create($validated);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'สร้างเอกสารซื้อ ' . $validated['doc_no'] . ' เรียบร้อยแล้ว');
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'branch']);
        return view('pages.purchase.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit(Purchase $purchase)
    {
        $suppliers = Customer::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();

        return view('pages.purchase.edit', compact('purchase', 'suppliers', 'branches'));
    }

    /**
     * Update the specified purchase.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'doc_no' => 'required|string|unique:purchases,doc_no,' . $purchase->id,
            'supplier_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'doc_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:doc_date',
            'subtotal' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'note' => 'nullable|string|max:1000',
            'status' => 'required|in:ชำระแล้ว,ค้างชำระ,ยกเลิก',
        ], [
            'doc_no.unique' => 'เลขที่เอกสารนี้มีอยู่ในระบบแล้ว',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ]);

        // Calculate VAT and Total
        $validated['vat'] = round($validated['subtotal'] * ($validated['vat_rate'] / 100), 2);
        $validated['total'] = $validated['subtotal'] + $validated['vat'];

        $purchase->update($validated);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'อัปเดตเอกสารซื้อ ' . $purchase->doc_no . ' เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified purchase (Hard Delete).
     */
    public function destroy(Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            $docNo = $purchase->doc_no;

            // 🔥 ลบจริง (Hard Delete)
            $purchase->forceDelete();

            DB::commit();

            return redirect()
                ->route('purchases.index')
                ->with('success', 'ลบเอกสารซื้อ ' . $docNo . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการลบเอกสาร: ' . $e->getMessage());
        }
    }

    /**
     * Remove methods that are not needed if you only want hard delete
     * Comment out or remove restore() and forceDelete() methods
     */

    // /**
    //  * Restore a soft-deleted purchase.
    //  */
    // public function restore($id)
    // {
    //     // ไม่ต้องใช้ถ้าลบจริง
    // }

    // /**
    //  * Force delete a purchase.
    //  */
    // public function forceDelete($id)
    // {
    //     // ไม่ต้องใช้แยกเพราะ destroy ทำ forceDelete แล้ว
    // }

    /**
     * Toggle purchase status.
     */
    public function toggleStatus(Purchase $purchase, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:ชำระแล้ว,ค้างชำระ,ยกเลิก',
            ]);

            $purchase->update(['status' => $request->status]);

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
     * Bulk delete purchases (Hard Delete).
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:purchases,id',
        ]);

        try {
            DB::beginTransaction();

            // 🔥 ลบจริงทั้งหมด
            $deletedCount = Purchase::whereIn('id', $request->ids)->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "ลบเอกสาร {$deletedCount} รายการเรียบร้อยแล้ว",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบเอกสาร: ' . $e->getMessage()
            ], 500);
        }
    }
}
