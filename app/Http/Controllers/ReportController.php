<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Expense;

use App\Models\Customer;
use App\Models\ChartOfAccount;

class ReportController extends Controller
{
    public function journal(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $startDate = $request->get(
            'start_date',
            now()->startOfMonth()->format('Y-m-d')
        );

        $endDate = $request->get(
            'end_date',
            now()->format('Y-m-d')
        );

        $journalType = $request->get(
            'journal_type',
            'sales'
        );

        $customerId = $request->get('customer_id');

        $rawItems = collect();

        /*
        |--------------------------------------------------------------------------
        | SALES JOURNAL
        |--------------------------------------------------------------------------
        */

        if ($journalType == 'sales') {

            $query = Sale::with('customer')

                ->whereBetween('doc_date', [
                    $startDate,
                    $endDate
                ])

                ->where('status', '!=', 'ยกเลิก');

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $rawItems = $query
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | PURCHASE JOURNAL
        |--------------------------------------------------------------------------
        */

        elseif ($journalType == 'purchase') {

            $query = Purchase::with('supplier')

                ->whereBetween('doc_date', [
                    $startDate,
                    $endDate
                ]);

            $rawItems = $query
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT JOURNAL
        |--------------------------------------------------------------------------
        */

        elseif ($journalType == 'payment') {

            $query = Payment::with('supplier')

                ->whereBetween('pay_date', [
                    $startDate,
                    $endDate
                ]);

            $rawItems = $query
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | RECEIPT JOURNAL
        |--------------------------------------------------------------------------
        */

        elseif ($journalType == 'receipt') {

            $query = Receipt::with('customer')

                ->whereBetween('receipt_date', [
                    $startDate,
                    $endDate
                ]);

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $rawItems = $query
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | EXPENSE JOURNAL
        |--------------------------------------------------------------------------
        */

        elseif ($journalType == 'expenses') {

            $query = Expense::with([
                'payee',
                'expenseAccount',
                'paymentAccount',
            ])

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT
            |--------------------------------------------------------------------------
            | เปลี่ยนชื่อ field ให้ตรง DB จริงของคุณ
            |--------------------------------------------------------------------------
            */

            ->whereBetween('expense_date', [
                $startDate,
                $endDate
            ]);

            if ($customerId) {
                $query->where('payee_id', $customerId);
            }

            $rawItems = $query
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | FORMAT TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $transactions = $rawItems

            ->map(function ($item) use ($journalType) {

                return $this->formatTransaction(
                    $item,
                    $journalType
                );
            })

            ->sortBy('date')

            ->values();

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $totals = [

            'total_debit' =>
                $transactions->sum('total'),

            'total_credit' =>
                $transactions->sum('subtotal')
                + $transactions->sum('vat'),
        ];

        /*
        |--------------------------------------------------------------------------
        | CUSTOMERS
        |--------------------------------------------------------------------------
        */

        $customers = Customer::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view('reports.journal', compact(
            'transactions',
            'totals',
            'customers',
            'customerId',
            'journalType',
            'startDate',
            'endDate'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT TRANSACTION
    |--------------------------------------------------------------------------
    */

    private function formatTransaction($item, $type)
    {
        /*
        |--------------------------------------------------------------------------
        | PARTY NAME
        |--------------------------------------------------------------------------
        */

        if (in_array($type, ['sales', 'receipt'])) {

            $partyName =
                $item->customer->name
                ?? 'ลูกค้าทั่วไป';
        }

        elseif ($type == 'expenses') {

            $partyName =
                $item->payee->name
                ?? 'ผู้รับเงิน';
        }

        else {

            $partyName =
                $item->supplier->name
                ?? 'ผู้จำหน่ายทั่วไป';
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $total = (float)(

            $item->grand_total
            ?? $item->total_amount
            ?? $item->total
            ?? $item->amount
            ?? 0
        );

        $vat = (float)(

            $item->vat_amount
            ?? $item->vat
            ?? 0
        );

        $subtotal = $total - $vat;

        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        $date =

            $item->expense_date
            ?? $item->doc_date
            ?? $item->pay_date
            ?? $item->receipt_date
            ?? now();

        /*
        |--------------------------------------------------------------------------
        | DOC NO
        |--------------------------------------------------------------------------
        */

        $docNo =

            $item->document_no
            ?? $item->doc_no
            ?? $item->invoice_no
            ?? $item->receipt_no
            ?? $item->reference_no
            ?? 'ERR-NO';

        /*
        |--------------------------------------------------------------------------
        | ACCOUNTS
        |--------------------------------------------------------------------------
        */

        if ($type == 'expenses') {

            $debitAccount =

                $item->expenseAccount
                    ? $item->expenseAccount->code . ' - ' .
                      ($item->expenseAccount->name_th
                        ?? $item->expenseAccount->name)
                    : $this->fetchAccountNameByCode('5300');

            $creditAccount =

                $item->paymentAccount
                    ? $item->paymentAccount->code . ' - ' .
                      ($item->paymentAccount->name_th
                        ?? $item->paymentAccount->name)
                    : $this->fetchAccountNameByCode('1110');
        }

        else {

            $debitAccount =
                $this->getAccountMapping($type, 'dr');

            $creditAccount =
                $this->getAccountMapping($type, 'cr');
        }

        return (object)[

            'date' => $date,

            'doc_no' => $docNo,

            'customer_name' => $partyName,

            'subtotal' => $subtotal,

            'vat' => $vat,

            'vat_rate' =>
                $item->vat_rate ?? 7,

            'total' => $total,

            'debit_account' => $debitAccount,

            'credit_account' => $creditAccount,

            'vat_account' =>
                $this->getVatAccountMapping($type),

            'status' =>
                $item->status ?? 'ปกติ',

            'rowspan' =>
                ($vat > 0) ? 3 : 2,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT MAPPING
    |--------------------------------------------------------------------------
    */

    private function getAccountMapping($type, $side)
    {
        $mapping = [

            /*
            |--------------------------------------------------------------------------
            | SALES
            |--------------------------------------------------------------------------
            */

            'sales' => [

                // ลูกหนี้การค้า
                'dr' => '1130',

                // รายได้จากการขาย
                'cr' => '4100',
            ],

            /*
            |--------------------------------------------------------------------------
            | PURCHASE
            |--------------------------------------------------------------------------
            */

            'purchase' => [

                // ซื้อสินค้า
                'dr' => '5110',

                // เจ้าหนี้การค้า
                'cr' => '2130',
            ],

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            'payment' => [

                // เจ้าหนี้การค้า
                'dr' => '2130',

                // เงินสด/ธนาคาร
                'cr' => '1110',
            ],

            /*
            |--------------------------------------------------------------------------
            | RECEIPT
            |--------------------------------------------------------------------------
            */

            'receipt' => [

                // เงินสด/ธนาคาร
                'dr' => '1110',

                // ลูกหนี้การค้า
                'cr' => '1130',
            ],

            /*
            |--------------------------------------------------------------------------
            | EXPENSES
            |--------------------------------------------------------------------------
            */

            'expenses' => [

                // ค่าใช้จ่าย
                'dr' => '5300',

                // เงินสด/ธนาคาร
                'cr' => '1110',
            ],
        ];

        $code = $mapping[$type][$side] ?? null;

        return $this->fetchAccountNameByCode($code);
    }

    /*
    |--------------------------------------------------------------------------
    | VAT ACCOUNT
    |--------------------------------------------------------------------------
    */

    private function getVatAccountMapping($type)
    {
        $vatCodes = [

            // ภาษีขาย
            'sales' => '2150',

            // ภาษีซื้อ
            'purchase' => '1150',

            // ภาษีซื้อค่าใช้จ่าย
            'expenses' => '1150',
        ];

        return $this->fetchAccountNameByCode(
            $vatCodes[$type] ?? null
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH ACCOUNT
    |--------------------------------------------------------------------------
    */

    private function fetchAccountNameByCode($code)
    {
        if (empty($code)) {
            return 'ไม่ระบุรหัสบัญชี';
        }

        $cleanCode = trim($code);

        static $accountCache = [];

        if (isset($accountCache[$cleanCode])) {
            return $accountCache[$cleanCode];
        }

        $account = ChartOfAccount::where('code', $cleanCode)

            ->orWhere('code', 'like', $cleanCode . '%')

            ->first();

        if ($account) {

            $displayName =

                $account->name_th
                ?: ($account->name ?? 'ไม่มีชื่อบัญชี');

            $result =
                "{$account->code} - {$displayName}";
        }

        else {

            $result =
                "{$cleanCode} - (ไม่พบในผังบัญชี)";
        }

        $accountCache[$cleanCode] = $result;

        return $result;
    }
}
