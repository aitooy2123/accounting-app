<?php

namespace App\Http\Controllers;

use App\Models\Sale; // ตรวจสอบชื่อ Model ของคุณ (อาจเป็น Sale หรือ Sales)
use Illuminate\Http\Request;
use App\Exports\SalesExport;
use App\Models\Branch;
use App\Models\Customer;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use COM;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doc_no', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        $sales = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('pages.sale.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $branches  = Branch::where('is_active', true)->orderBy('name')->get();
        return view('pages.sale.create', compact('customers', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'items' => 'required|array|min:1',
            'items.*.desc' => 'required',
            'items.*.qty' => 'required|numeric|min:0.1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = collect($request->items)->sum(fn($i) => $i['qty'] * $i['price']);
            $vat = $subtotal * 0.07;

            $sale = Sale::create([
                'doc_no' => 'INV-' . strtoupper(uniqid()),
                'customer_id' => $request->customer_id,
                'branch_id' => $request->branch_id,
                'doc_date' => $request->doc_date,
                'due_date' => now()->parse($request->doc_date)->addDays($request->credit_term),
                'subtotal' => $subtotal,
                'vat' => $vat,
                'total' => $subtotal + $vat,
                'note' => $request->note
            ]);

            foreach ($request->items as $item) {
                $sale->items()->create([
                    'description' => $item['desc'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'total' => $item['qty'] * $item['price']
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'บันทึกสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // ดึงข้อมูล Sale พร้อมรายการสินค้า (Eager Loading)
        $sale = Sale::with('items')->findOrFail($id);

        // จำลองข้อมูลลูกค้าและสาขา (เหมือนหน้า Create)
        $customers = collect([
            (object)['id' => 1, 'name' => 'บจก. ไทยโพสต์', 'tax_id' => '0105546000002', 'address' => 'หลักสี่ กทม.'],
            (object)['id' => 2, 'name' => 'เอบีซี เทรดดิ้ง', 'tax_id' => '0105560000001', 'address' => 'บางนา กทม.']
        ]);
        $branches = collect([(object)['id' => 0, 'name' => 'สำนักงานใหญ่'], (object)['id' => 1, 'name' => 'สาขา 1']]);

        return view('pages.sale.edit', compact('sale', 'customers', 'branches'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validation
        $request->validate([
            'customer_id' => 'required',
            'items' => 'required|array|min:1',
            'items.*.desc' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $sale = Sale::findOrFail($id);

                // 2. คำนวณยอดเงิน Subtotal
                $subtotal = collect($request->items)->sum(fn($item) => $item['qty'] * $item['price']);

                // --- จุดที่แก้ไข: เช็ค Toggle จากหน้าเว็บ ---
                // ถ้าติ๊กถูกมา $request->is_vat จะมีค่า (เช่น "1" หรือ "on")
                // ถ้าไม่ติ๊กมา ค่าจะเป็น null หรือ 0 (ขึ้นอยู่กับว่าเราใส่ hidden input ไหม)
                $vat = 0;
                if ($request->has('is_vat') && $request->is_vat == '1') {
                    $vat = $subtotal * 0.07;
                }
                // ---------------------------------------

                $total = $subtotal + $vat;

                // 3. อัปเดตข้อมูลที่ตารางหลัก (sales)
                $sale->update([
                    'customer_id' => $request->customer_id,
                    'status' => $request->status,
                    'note' => $request->note,
                    'subtotal' => $subtotal,
                    'vat' => $vat,
                    'total' => $total,
                    'doc_date' => $request->doc_date, // อย่าลืมเก็บวันที่ถ้ามีการแก้ไข
                ]);

                // 4. ลบรายการสินค้าเดิม
                $sale->items()->delete();

                // 5. บันทึกรายการสินค้าใหม่
                foreach ($request->items as $item) {
                    $sale->items()->create([
                        'description' => $item['desc'],
                        'quantity' => $item['qty'],
                        'unit_price' => $item['price'],
                        'total' => $item['qty'] * $item['price'],
                    ]);
                }

                return redirect()->route('sales.index')->with('success', 'อัปเดตรายการเรียบร้อยแล้ว');
            });
        } catch (\Exception $e) {
            return back()->withErrors('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $docNo = $sale->doc_no;
        $sale->delete(); // SaleItem จะโดนลบด้วยถ้าตั้ง onDelete('cascade') ใน Migration

        return redirect()->route('sales.index')->with('success', "ลบเอกสาร $docNo เรียบร้อยแล้ว");
    }

    public function pdf($id)
    {
        $sale = Sale::findOrFail($id);

        $pdf = Pdf::loadView('pages.sale.pdf', compact('sale'))
            ->setPaper('a4')
            ->setOption([
                'defaultFont' => 'Kanthit', // เปลี่ยนจาก THSarabunNew เป็น Kanthit
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true, // ช่วยเรื่องการจัดโครงสร้าง HTML
                'isFontSubsettingEnabled' => true // ช่วยลดขนาดไฟล์ PDF โดยดึงเฉพาะตัวอักษรที่ใช้
            ]);

        return $pdf->stream($sale->doc_no . '.pdf');
    }

    public function generatePdf($id)
    {
        $sale = Sale::findOrFail($id);

        $data = [
            'sale' => $sale,
            'title' => 'ใบแจ้งหนี้ ' . $sale->doc_no
        ];

        // โหลด view สำหรับทำ PDF (ต้องสร้างไฟล์ resources/views/pages/sales_pdf_view.blade.php)
        $pdf = Pdf::loadView('pages.sales_pdf_view', $data)
            ->setPaper('a4')
            ->setOption(['defaultFont' => 'THSarabunNew']);

        return $pdf->stream($sale->doc_no . '.pdf');
    }
}
