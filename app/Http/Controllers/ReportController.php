<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function journal(Request $request)
    {
        $startDate   = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate     = $request->get('end_date', now()->format('Y-m-d'));
        $journalType = $request->get('journal_type', 'sales');
        $customerId  = $request->get('customer_id');

        $transactions = collect();

        /*
        |--------------------------------------------------------------------------
        | สมุดรายวันขาย (SJ)
        |--------------------------------------------------------------------------
        */
        if ($journalType == 'sales') {

            $query = Sale::with('customer', 'items')
                ->whereBetween('doc_date', [$startDate, $endDate])
                ->where('status', '!=', 'ยกเลิก');

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $transactions = $query->get()->map(function ($item) {
                return $this->formatTransaction($item, 'sales');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | สมุดรายวันซื้อ (PJ)
        |--------------------------------------------------------------------------
        */
        elseif ($journalType == 'purchase') {

            $query = Purchase::with('supplier', 'items')
                ->whereBetween('doc_date', [$startDate, $endDate]);

            if ($customerId) {
                $query->where('supplier_id', $customerId);
            }

            $transactions = $query->get()->map(function ($item) {
                return $this->formatTransaction($item, 'purchase');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | สมุดรายวันจ่าย (PV)
        |--------------------------------------------------------------------------
        */
        elseif ($journalType == 'payment') {

            $query = Payment::with('supplier')
                ->whereBetween('pay_date', [$startDate, $endDate])
                ->where('type', 'payment');

            $transactions = $query->get()->map(function ($item) {
                return $this->formatTransaction($item, 'payment');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | สมุดรายวันรับ (RV)
        |--------------------------------------------------------------------------
        */
        elseif ($journalType == 'receipt') {

            $query = Payment::with('customer')
                ->whereBetween('pay_date', [$startDate, $endDate])
                ->where('type', 'receipt');

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $transactions = $query->get()->map(function ($item) {
                return $this->formatTransaction($item, 'receipt');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | สมุดรายวันทั่วไป (GJ)
        |--------------------------------------------------------------------------
        */
        else {

            $transactions = collect();
        }

        /*
        |--------------------------------------------------------------------------
        | เรียงข้อมูล
        |--------------------------------------------------------------------------
        */
        $transactions = $transactions->sortBy('date');

        /*
        |--------------------------------------------------------------------------
        | ยอดรวม
        |--------------------------------------------------------------------------
        */
        $totals = [
            'total_debit'  => $transactions->sum('total'),
            'total_credit' => $transactions->sum('total'),
        ];

        /*
        |--------------------------------------------------------------------------
        | ลูกค้า
        |--------------------------------------------------------------------------
        */
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
     * Format Transaction
     */
    private function formatTransaction($item, $type)
    {
        /*
        |--------------------------------------------------------------------------
        | ชื่อคู่ค้า
        |--------------------------------------------------------------------------
        */
        $partyName = '-';

        if ($type == 'sales' || $type == 'receipt') {

            $partyName = $item->customer->name ?? 'ลูกค้าทั่วไป';

        } else {

            $partyName = $item->supplier->name ?? 'ผู้จำหน่ายทั่วไป';
        }

        /*
        |--------------------------------------------------------------------------
        | วันที่เอกสาร
        |--------------------------------------------------------------------------
        */
        $date = $item->doc_date
            ?? $item->pay_date
            ?? now();

        /*
        |--------------------------------------------------------------------------
        | เลขที่เอกสาร
        |--------------------------------------------------------------------------
        */
        $docNo = $item->doc_no
            ?? $item->code
            ?? 'ERR-NO';

        /*
        |--------------------------------------------------------------------------
        | จำนวนเงิน
        |--------------------------------------------------------------------------
        */
        $total = (float)(
            $item->amount
            ?? $item->total
            ?? 0
        );

        $subtotal = (float)(
            $item->subtotal
            ?? $item->amount
            ?? 0
        );

        $vat = (float)(
            $item->vat
            ?? 0
        );

        return (object)[

            'date' => $date,

            'doc_no' => $docNo,

            'customer_name' => $partyName,

            'total' => $total,

            'subtotal' => $subtotal,

            'vat' => $vat,

            'vat_rate' => $item->vat_rate ?? 7,

            'debit_account' => $this->getAccountName($type, 'dr'),

            'credit_account' => $this->getAccountName($type, 'cr'),

            'rowspan' => ($vat > 0) ? 3 : 2,
        ];
    }

    /**
     * ชื่อบัญชี
     */
    private function getAccountName($type, $side)
    {
        $accounts = [

            /*
            |--------------------------------------------------------------------------
            | สมุดรายวันขาย (SJ)
            |--------------------------------------------------------------------------
            */
            'sales' => [
                'dr' => 'ลูกหนี้การค้า',
                'cr' => 'รายได้จากการขาย',
            ],

            /*
            |--------------------------------------------------------------------------
            | สมุดรายวันซื้อ (PJ)
            |--------------------------------------------------------------------------
            */
            'purchase' => [
                'dr' => 'ซื้อสินค้า/ต้นทุน',
                'cr' => 'เจ้าหนี้การค้า',
            ],

            /*
            |--------------------------------------------------------------------------
            | สมุดรายวันจ่าย (PV)
            |--------------------------------------------------------------------------
            */
            'payment' => [
                'dr' => 'เจ้าหนี้การค้า',
                'cr' => 'เงินสด/เงินฝากธนาคาร',
            ],

            /*
            |--------------------------------------------------------------------------
            | สมุดรายวันรับ (RV)
            |--------------------------------------------------------------------------
            */
            'receipt' => [
                'dr' => 'เงินสด/เงินฝากธนาคาร',
                'cr' => 'ลูกหนี้การค้า',
            ],
        ];

        return $accounts[$type][$side] ?? 'รอนำเข้าบัญชี';
    }
}
