<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ย้ายมาไว้ตรงนี้ที่เดียว

class PurchaseController extends Controller
{
    /**
     * รายการใบสั่งซื้อทั้งหมด
     */
    public function index(Request $request)
    {
        $query = Purchase::query()->with(['supplier', 'branch']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
     * หน้าสร้างใบสั่งซื้อใหม่
     */
    public function create()
    {
        $suppliers = Customer::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();
        $autoDocNo = Purchase::generateDocNo();

        return view('pages.purchase.create', compact('suppliers', 'branches', 'autoDocNo'));
    }

    /**
     * บันทึกใบสั่งซื้อใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doc_no' => 'required|string|unique:purchases,doc_no',
            'supplier_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'doc_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:doc_date',
            'subtotal' => 'required|numeric',
            'vat_rate' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.desc' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. บันทึกหัวเอกสาร
            $purchase = Purchase::create([
                'doc_no' => $validated['doc_no'],
                'supplier_id' => $validated['supplier_id'],
                'branch_id' => $validated['branch_id'],
                'doc_date' => $validated['doc_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $request->subtotal,
                'vat' => $request->vat,
                'total' => $request->total,
                'vat_rate' => $validated['vat_rate'],
                'note' => $request->note,
                'status' => 'ค้างชำระ',
            ]);

            // 2. บันทึกรายการสินค้า
            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'desc' => $item['desc'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'บันทึกเอกสาร ' . $purchase->doc_no . ' เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * แสดงรายละเอียดใบสั่งซื้อ
     */
    public function show(Purchase $purchase)
    {
        // โหลดรายการสินค้ามาด้วยเพื่อให้หน้า Show แสดงตารางสินค้าได้
        $purchase->load(['items', 'supplier', 'branch']);
        return view('pages.purchase.show', compact('purchase'));
    }

    /**
     * หน้าแก้ไขใบสั่งซื้อ
     */
    public function edit(Purchase $purchase)
    {
        $suppliers = Customer::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();
        $purchase->load('items'); // โหลด items มาแสดงในตารางแก้ไข

        return view('pages.purchase.edit', compact('purchase', 'suppliers', 'branches'));
    }

    /**
     * อัปเดตใบสั่งซื้อ
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
            'items' => 'required|array|min:1',
            'items.*.desc' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ], [
            'doc_no.unique' => 'เลขที่เอกสารนี้มีอยู่ในระบบแล้ว',
            'items.required' => 'กรุณาระบุรายการสินค้าอย่างน้อย 1 รายการ',
        ]);

        try {
            DB::beginTransaction();

            $vat = round($request->subtotal * ($request->vat_rate / 100), 2);
            $total = $request->subtotal + $vat;

            // 1. อัปเดตหัวเอกสาร
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'branch_id'   => $request->branch_id,
                'doc_date'    => $request->doc_date,
                'due_date'    => $request->due_date,
                'subtotal'    => $request->subtotal,
                'vat'         => $vat,
                'vat_rate'    => $request->vat_rate,
                'total'       => $total,
                'note'        => $request->note,
                'status'      => $request->status,
            ]);

            // 2. อัปเดตรายการสินค้า (ลบของเดิม สร้างใหม่)
            $purchase->items()->delete();
            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'desc'  => $item['desc'],
                    'qty'   => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'อัปเดตเอกสารซื้อ ' . $purchase->doc_no . ' เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ลบใบสั่งซื้อ (Hard Delete)
     */
    public function destroy(Purchase $purchase)
    {
        try {
            // หมายเหตุ: เนื่องจากตั้ง cascade ใน Database หรือลบ manual
            // รายการใน purchase_items จะถูกลบตามความสัมพันธ์
            $purchase->forceDelete();

            return redirect()->route('purchases.index')->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'ลบไม่สำเร็จ: ' . $e->getMessage());
        }
    }

    /**
     * เปลี่ยนสถานะผ่าน AJAX
     */
    public function toggleStatus(Purchase $purchase, Request $request)
    {
        try {
            $request->validate(['status' => 'required|in:ชำระแล้ว,ค้างชำระ,ยกเลิก']);
            $purchase->update(['status' => $request->status]);

            return response()->json(['success' => true, 'message' => 'อัปเดตสถานะเรียบร้อย']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ลบหลายรายการพร้อมกัน
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:purchases,id',
        ]);

        try {
            DB::beginTransaction();
            $deletedCount = Purchase::whereIn('id', $request->ids)->forceDelete();
            DB::commit();

            return response()->json(['success' => true, 'message' => "ลบเอกสาร {$deletedCount} รายการเรียบร้อยแล้ว"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
