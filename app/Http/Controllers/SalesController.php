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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doc_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(10);
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
}
