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
     * Remove the specified purchase (Soft Delete).
     */
    public function destroy(Purchase $purchase)
    {
        try {
            $docNo = $purchase->doc_no;
            $purchase->delete(); // Soft delete

            return redirect()
                ->route('purchases.index')
                ->with('success', 'ลบเอกสารซื้อ ' . $docNo . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการลบเอกสาร: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted purchase.
     */
    public function restore($id)
    {
        try {
            $purchase = Purchase::withTrashed()->findOrFail($id);
            $purchase->restore();

            return redirect()
                ->route('purchases.index')
                ->with('success', 'กู้คืนเอกสารซื้อ ' . $purchase->doc_no . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการกู้คืนเอกสาร');
        }
    }

    /**
     * Force delete a purchase.
     */
    public function forceDelete($id)
    {
        try {
            $purchase = Purchase::withTrashed()->findOrFail($id);
            $docNo = $purchase->doc_no;
            $purchase->forceDelete();

            return redirect()
                ->route('purchases.index')
                ->with('success', 'ลบเอกสารซื้อ ' . $docNo . ' แบบถาวรเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการลบเอกสารแบบถาวร');
        }
    }

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
     * Bulk delete purchases (Soft Delete).
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:purchases,id'
        ], [
            'ids.required' => 'กรุณาเลือกเอกสารอย่างน้อย 1 รายการ',
            'ids.min' => 'กรุณาเลือกเอกสารอย่างน้อย 1 รายการ',
            'ids.*.exists' => 'ไม่พบเอกสารที่เลือกในระบบ',
        ]);

        try {
            DB::beginTransaction();

            $purchases = Purchase::whereIn('id', $request->ids)->get();
            $count = $purchases->count();
            $failedDocs = [];

            foreach ($purchases as $purchase) {
                try {
                    $purchase->delete(); // Soft delete
                } catch (\Exception $e) {
                    $failedDocs[] = $purchase->doc_no;
                }
            }

            $deletedCount = $count - count($failedDocs);

            DB::commit();

            if ($deletedCount > 0 && count($failedDocs) === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "ลบเอกสารซื้อ {$deletedCount} รายการเรียบร้อยแล้ว",
                    'deleted_count' => $deletedCount
                ]);
            } elseif ($deletedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "ลบเอกสาร {$deletedCount} รายการ แต่มี " . count($failedDocs) . " รายการที่ไม่สามารถลบได้",
                    'deleted_count' => $deletedCount,
                    'failed' => $failedDocs
                ]);
            } else {
                throw new \Exception("ไม่สามารถลบเอกสารที่เลือกได้");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }
}
