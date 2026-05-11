<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase; // สมมติว่ามี Model Purchase
use App\Models\Payment;  // สมมติว่ามี Model Payment
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function journal(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $journalType = $request->get('journal_type', 'sales'); // default เป็น sales
        $customerId = $request->get('customer_id');

        $transactions = collect();

        // --- 1. ดึงข้อมูลตามประเภทสมุดรายวัน ---

        if ($journalType == 'sales') {
            // สมุดรายวันขาย (SJ)
            $query = Sale::with('customer', 'items')
                ->whereBetween('doc_date', [$startDate, $endDate])
                ->where('status', '!=', 'ยกเลิก');

            if ($customerId) $query->where('customer_id', $customerId);

            $transactions = $query->get()->map(function ($item) {
                return $this->formatTransaction($item, 'sales');
            });

        } elseif ($journalType == 'purchase') {
            // สมุดรายวันซื้อ (PJ)
            // สมมติว่าใช้ Model Purchase และมีโครงสร้างคล้าย Sale
            $query = Purchase::with('supplier', 'items')
                ->whereBetween('doc_date', [$startDate, $endDate]);

            $transactions = $query->get()->map(function ($item) {
                return $this->formatTransaction($item, 'purchase');
            });

        } elseif ($journalType == 'payment') {
            // สมุดรายวันจ่าย (PV)
            $query = Payment::with('supplier')
                ->whereBetween('pay_date', [$startDate, $endDate]);

            $transactions = $query->get()->map(function ($item) {
                return $this->formatTransaction($item, 'payment');
            });
        }

        // --- 2. การจัดการยอดรวมและตัวแปรส่งไปหน้า View ---

        $transactions = $transactions->sortBy('date');

        $totals = [
            'total_debit' => $transactions->sum('total'),
            'total_credit' => $transactions->sum('total'),
        ];

        $customers = Customer::orderBy('name')->get();

        return view('reports.journal', compact(
            'transactions',
            'totals',
            'customers',
            'customerId',
            'journalType'
        ));
    }

    /**
     * Helper สำหรับจัดรูปแบบข้อมูล Double-Entry ตามประเภทสมุด
     */
private function formatTransaction($item, $type)
{
    // ดักจับกรณีไม่มีความสัมพันธ์ (Relation) ของ Customer หรือ Supplier
    $partyName = '-';
    if ($type == 'sales') {
        $partyName = $item->customer->name ?? 'ลูกค้าทั่วไป';
    } else {
        $partyName = $item->supplier->name ?? 'ผู้จำหน่ายทั่วไป';
    }

    return (object)[
        'date' => $item->doc_date ?? ($item->pay_date ?? now()),
        'doc_no' => $item->doc_no ?? 'ERR-NO',
        'customer_name' => $partyName,
        'total' => (float)($item->amount ?? ($item->total ?? 0)),
        'subtotal' => (float)($item->subtotal ?? 0),
        'vat' => (float)($item->vat ?? 0),
        'vat_rate' => $item->vat_rate ?? 7,
        'debit_account' => $this->getAccountName($type, 'dr'),
        'credit_account' => $this->getAccountName($type, 'cr'),
        'rowspan' => ($item->vat > 0) ? 3 : 2,
    ];
}

// แยก Logic ชื่อบัญชีออกมาให้ดูสะอาดขึ้น
private function getAccountName($type, $side) {
    $accounts = [
        'sales'    => ['dr' => 'ลูกหนี้การค้า', 'cr' => 'รายได้จากการขาย'],
        'purchase' => ['dr' => 'ซื้อสินค้า/ต้นทุน', 'cr' => 'เจ้าหนี้การค้า'],
        'payment'  => ['dr' => 'เจ้าหนี้การค้า', 'cr' => 'เงินสด/เงินฝากธนาคาร'],
    ];
    return $accounts[$type][$side] ?? 'รอนำเข้าบัญชี';
}

}
