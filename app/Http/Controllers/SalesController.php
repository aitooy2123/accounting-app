<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'branch']);

        // ค้นหา
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doc_no', 'like', "%{$search}%")
                    ->orWhere('customer_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // กรองสถานะ
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // เรียงลำดับ
        $query->orderBy('doc_date', 'desc');

        $sales = $query->paginate(10);
        $sales->appends($request->query());

        return view('pages.sale.index', compact('sales'));
    }
    public function create()
    {
        $customers = Customer::with(['company', 'branch'])->orderBy('name')->get();
        $branches  = Branch::where('is_active', true)->orderBy('name')->get();
        return view('pages.sale.create', compact('customers', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id'   => 'required|exists:branches,id',
            'doc_date'    => 'required|date',
            'credit_term' => 'required|integer|in:0,7,30',
            'vat_rate'    => 'required|in:0,7,10',
            'note'        => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.desc' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['qty'] * $item['price'];
            }
            $vat = $subtotal * ($request->vat_rate / 100);
            $total = $subtotal + $vat;

            // Generate document number
            $docNo = $this->generateDocNo();

            // Create sale
            $sale = Sale::create([
                'doc_no'      => $docNo,
                'customer_id' => $request->customer_id,
                'branch_id'   => $request->branch_id,
                'doc_date'    => $request->doc_date,
                'credit_term' => $request->credit_term,
                'due_date'    => Carbon::parse($request->doc_date)->addDays($request->credit_term),
                'vat_rate'    => $request->vat_rate,
                'subtotal'    => $subtotal,
                'vat'         => $vat,
                'total'       => $total,
                'note'        => $request->note,
            ]);

            // Create sale items
            foreach ($request->items as $item) {
                $sale->items()->create([
                    'description' => $item['desc'],
                    'quantity'    => $item['qty'],
                    'unit_price'  => $item['price'],
                    'total_price' => $item['qty'] * $item['price'],
                    'total'       => ($item['qty'] * $item['price']) + ($item['qty'] * $item['price'] * ($request->vat_rate / 100)),
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'สร้างเอกสารขายเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Sale $sale)
    {
        $sale->load(['items', 'customer.company', 'branch']);
        $customers = Customer::with(['company', 'branch'])->orderBy('name')->get();
        $branches  = Branch::where('is_active', true)->orderBy('name')->get();
        return view('pages.sale.edit', compact('sale', 'customers', 'branches'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id'   => 'required|exists:branches,id',
            'doc_date'    => 'required|date',
            'credit_term' => 'required|integer|in:0,7,30',
            'vat_rate'    => 'required|in:0,7,10',
            'note'        => 'nullable|string',
            'status' => 'nullable|in:ชำระแล้ว,ค้างชำระ,ออกใบเสอนราคา',
            'items'       => 'required|array|min:1',
            'items.*.desc' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Recalculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['qty'] * $item['price'];
            }
            $vat = $subtotal * ($request->vat_rate / 100);
            $total = $subtotal + $vat;

            // Update sale
            $sale->update([
                'customer_id' => $request->customer_id,
                'branch_id'   => $request->branch_id,
                'doc_date'    => $request->doc_date,
                'credit_term' => $request->credit_term,
                'due_date'    => Carbon::parse($request->doc_date)->addDays($request->credit_term),
                'vat_rate'    => $request->vat_rate,
                'status' => $request->status ?? $sale->status,
                'subtotal'    => $subtotal,
                'vat'         => $vat,
                'total'       => $total,
                'note'        => $request->note,
            ]);

            // Delete old items and recreate
            $sale->items()->delete();
            foreach ($request->items as $item) {
                $sale->items()->create([
                    'description' => $item['desc'],
                    'quantity'    => $item['qty'],
                    'unit_price'  => $item['price'],
                    'total'       => $item['qty'] * $item['price'], // Change this line
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'อัปเดตเอกสารขายเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Sale $sale)
    {
        $docNo = $sale->doc_no;
        $sale->delete(); // Items will be deleted automatically if cascade is set in migration
        return redirect()->route('sales.index')->with('success', "ลบเอกสาร {$docNo} เรียบร้อยแล้ว");
    }

    public function pdf($id)
    {
        $sale = Sale::with(['customer', 'branch', 'items'])->findOrFail($id);
        $pdf = Pdf::loadView('pages.sale.pdf', compact('sale'))
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'Kanthit',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);
        return $pdf->stream($sale->doc_no . '.pdf');
    }

    private function generateDocNo()
    {
        $year = date('Y');
        $lastSale = Sale::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        if ($lastSale) {
            $lastNo = intval(substr($lastSale->doc_no, -5));
            $newNo = str_pad($lastNo + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNo = '00001';
        }
        return 'INV-' . $year . '-' . $newNo;
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:sales,id'
        ], [
            'ids.required' => 'กรุณาเลือกรายการอย่างน้อย 1 รายการ',
            'ids.array' => 'รูปแบบข้อมูลไม่ถูกต้อง',
            'ids.min' => 'กรุณาเลือกรายการอย่างน้อย 1 รายการ',
            'ids.*.exists' => 'ไม่พบรายการที่เลือกในระบบ'
        ]);

        try {
            DB::beginTransaction();

            $sales = Sale::whereIn('id', $request->ids)->get();
            $count = $sales->count();

            // Optional: Add business logic checks
            // foreach ($sales as $sale) {
            //     if ($sale->payments()->exists()) {
            //         throw new \Exception("เอกสาร {$sale->doc_no} มีรายการชำระเงินอยู่ ไม่สามารถลบได้");
            //     }
            // }

            Sale::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "ลบเอกสาร {$count} รายการเรียบร้อยแล้ว",
                'deleted_count' => $count
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

public function show(Sale $sale)
{
    $customers = Customer::active()->orderBy('name')->get();
    $branches = Branch::active()->orderBy('name')->get();
    return view('pages.sale.show', compact('sale', 'customers', 'branches'));
}

public function showsale($id)
{
    // ใช้ Sale:: (โมเดล) เพื่อหาข้อมูล
    $sale = Sale::with(['customer', 'items'])->find($id);

    if (!$sale) {
        return redirect()->back()->with('error', 'ไม่พบข้อมูล');
    }

    // มั่นใจว่าชื่อไฟล์ view และโฟลเดอร์ถูกต้อง
return view('pages.quotations.saleshow', compact('sale'));}
}
