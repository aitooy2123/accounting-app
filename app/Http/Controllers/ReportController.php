<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function journal(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $documentType = $request->get('document_type');
        $customerId = $request->get('customer_id'); // เพิ่มตัวกรองลูกค้า

        $transactions = collect();

        // สร้าง Query สำหรับ Sale
        $query = Sale::with('customer', 'items')
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->where('status', '!=', 'ยกเลิก');

        // กรองตามลูกค้า (ถ้ามีการเลือก)
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        // กรองตามประเภทเอกสาร
        if (!$documentType || $documentType == 'quotation') {
            $quotations = $query->get()->map(function ($sale) {
                return (object)[
                    'date' => $sale->doc_date,
                    'doc_no' => $sale->doc_no,
                    'customer_id' => $sale->customer_id,
                    'customer_name' => $sale->customer->name ?? 'ลูกค้าทั่วไป',
                    'customer_tax_id' => $sale->customer->tax_id ?? '-',
                    'description' => $sale->items->first()->description ?? 'ขายสินค้า',
                    'total' => $sale->total,
                    'subtotal' => $sale->subtotal,
                    'vat' => $sale->vat,
                    'vat_rate' => $sale->vat_rate,
                    'type' => 'quotation',
                    'rowspan' => $sale->vat > 0 ? 3 : 2,
                ];
            });
            $transactions = $transactions->merge($quotations);
        }

        // เรียงลำดับตามวันที่และลูกค้า
        $transactions = $transactions->sortBy(['date', 'customer_name']);

        // คำนวณยอดรวม
        $totals = [
            'total_debit' => $transactions->sum('total'),
            'total_credit' => $transactions->sum('total'),
        ];

        // ดึงรายชื่อลูกค้าทั้งหมดสำหรับใช้ใน dropdown filter
        $customers = Customer::orderBy('name')->get();

        // สรุปยอดแยกลูกค้า (Customer Summary)
        $customerSummary = $transactions->groupBy('customer_id')->map(function ($items, $customerId) {
            $firstItem = $items->first();
            return (object)[
                'customer_id' => $customerId,
                'customer_name' => $firstItem->customer_name,
                'customer_tax_id' => $firstItem->customer_tax_id,
                'total_amount' => $items->sum('total'),
                'transaction_count' => $items->count(),
            ];
        })->values();

        return view('reports.journal', compact('transactions', 'totals', 'customers', 'customerSummary', 'customerId'));
    }

    // รายงานแยกรายลูกค้า (Customer Statement)
    public function customerStatement(Request $request, $customerId = null)
    {
        $customer = Customer::findOrFail($customerId ?? $request->get('customer_id'));

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $transactions = Sale::with('items')
            ->where('customer_id', $customer->id)
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->where('status', '!=', 'ยกเลิก')
            ->orderBy('doc_date')
            ->get()
            ->map(function ($sale) {
                return (object)[
                    'date' => $sale->doc_date,
                    'doc_no' => $sale->doc_no,
                    'description' => $sale->items->first()->description ?? 'ขายสินค้า',
                    'amount' => $sale->total,
                    'balance' => 0, // จะคำนวณยอดสะสมทีหลัง
                ];
            });

        // คำนวณยอดสะสม
        $runningBalance = 0;
        foreach ($transactions as $transaction) {
            $runningBalance += $transaction->amount;
            $transaction->balance = $runningBalance;
        }

        $totalAmount = $transactions->sum('amount');

        return view('reports.customer-statement', compact('customer', 'transactions', 'startDate', 'endDate', 'totalAmount'));
    }

    // Export รายงานแยกลูกค้าเป็น PDF
    public function exportCustomerStatement($customerId, Request $request)
    {
        // สามารถใช้ Barryvdh\DomPDF หรือ Laravel PDF
        // $pdf = PDF::loadView('reports.pdf.customer-statement', $data);
        // return $pdf->download('customer-statement.pdf');
    }
}
