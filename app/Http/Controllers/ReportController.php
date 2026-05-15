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
        | FILTER & PARAMS
        |--------------------------------------------------------------------------
        */
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));
        $journalType = $request->get('journal_type', 'sales');
        $customerId = $request->get('customer_id');

        $rawItems = collect();

        /*
        |--------------------------------------------------------------------------
        | DATA FETCHING (BY TYPE)
        |--------------------------------------------------------------------------
        */
        if ($journalType == 'sales') {
            $query = Sale::with('customer')
                ->whereBetween('doc_date', [$startDate, $endDate])
                ->where('status', '!=', 'ยกเลิก');

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            $rawItems = $query->latest()->get();

        } elseif ($journalType == 'purchase') {
            $query = Purchase::with('supplier')
                ->whereBetween('doc_date', [$startDate, $endDate]);
            $rawItems = $query->latest()->get();

        } elseif ($journalType == 'payment') {
            $query = Payment::with('supplier')
                ->whereBetween('pay_date', [$startDate, $endDate]);
            $rawItems = $query->latest()->get();

        } elseif ($journalType == 'receipt') {
            $query = Receipt::with('customer')
                ->whereBetween('receipt_date', [$startDate, $endDate]);

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            $rawItems = $query->latest()->get();

        } elseif ($journalType == 'expenses') {
            $query = Expense::with(['payee', 'account']) // ใช้ 'account' ตาม Model ที่เราแก้ไขล่าสุด
                ->whereBetween('expense_date', [$startDate, $endDate]);

            if ($customerId) {
                $query->where('payee_id', $customerId);
            }
            $rawItems = $query->latest()->get();
        }

        /*
        |--------------------------------------------------------------------------
        | FORMAT TRANSACTIONS FOR VIEW
        |--------------------------------------------------------------------------
        */
        $transactions = $rawItems->map(function ($item) use ($journalType) {
            return $this->formatTransaction($item, $journalType);
        })->sortBy('date')->values();

        // คำนวณผลรวม
        $totals = [
            'total_debit'  => $transactions->sum('total'),
            'total_credit' => $transactions->sum('subtotal') + $transactions->sum('vat'),
        ];

        $customers = Customer::orderBy('name')->get();

        return view('reports.journal', compact(
            'transactions', 'totals', 'customers',
            'customerId', 'journalType', 'startDate', 'endDate'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT TRANSACTION LOGIC
    |--------------------------------------------------------------------------
    */
    private function formatTransaction($item, $type)
    {
        // 1. หาชื่อคู่ค้า (Party Name)
        if (in_array($type, ['sales', 'receipt'])) {
            $partyName = $item->customer->name ?? 'ลูกค้าทั่วไป';
        } elseif ($type == 'expenses') {
            $partyName = $item->payee->name ?? 'ผู้รับเงิน';
        } else {
            $partyName = $item->supplier->name ?? 'ผู้จำหน่ายทั่วไป';
        }

        // 2. คำนวณตัวเลข
        $total = (float)($item->grand_total ?? $item->total_amount ?? $item->total ?? $item->amount ?? 0);
        $vat   = (float)($item->vat_amount ?? $item->vat ?? 0);
        $subtotal = $total - $vat;

        // 3. จัดการวันที่และเลขที่เอกสาร
        $date = $item->expense_date ?? $item->doc_date ?? $item->pay_date ?? $item->receipt_date ?? now();
        $docNo = $item->document_no ?? $item->doc_no ?? $item->invoice_no ?? $item->receipt_no ?? $item->reference_no ?? 'N/A';

        // 4. ผังบัญชี Debit / Credit
        if ($type == 'expenses') {
            // Debit: ใช้ Account ที่เลือกในหน้า Expense
            $debitAccount = $item->account
                ? $item->account->code . ' - ' . ($item->account->name_th ?? $item->account->name)
                : $this->fetchAccountNameByCode('5300'); // Default หมวดค่าใช้จ่าย

            // Credit: ปกติจะเป็นเงินสด/ธนาคาร
            $creditAccount = $this->fetchAccountNameByCode('1110');
        } else {
            $debitAccount = $this->getAccountMapping($type, 'dr');
            $creditAccount = $this->getAccountMapping($type, 'cr');
        }

        return (object)[
            'date'           => $date,
            'doc_no'         => $docNo,
            'customer_name'  => $partyName,
            'subtotal'       => $subtotal,
            'vat'            => $vat,
            'vat_rate'       => $item->vat_rate ?? 7,
            'total'          => $total,
            'debit_account'  => $debitAccount,
            'credit_account' => $creditAccount,
            'vat_account'    => $this->getVatAccountMapping($type),
            'status'         => $item->status ?? 'ปกติ',
            'rowspan'        => ($vat > 0) ? 3 : 2,
        ];
    }

    private function getAccountMapping($type, $side)
    {
        $mapping = [
            'sales'    => ['dr' => '1130', 'cr' => '4100'],
            'purchase' => ['dr' => '5110', 'cr' => '2130'],
            'payment'  => ['dr' => '2130', 'cr' => '1110'],
            'receipt'  => ['dr' => '1110', 'cr' => '1130'],
            'expenses' => ['dr' => '5300', 'cr' => '1110'],
        ];
        $code = $mapping[$type][$side] ?? null;
        return $this->fetchAccountNameByCode($code);
    }

    private function getVatAccountMapping($type)
    {
        $vatCodes = [
            'sales'    => '2150',
            'purchase' => '1150',
            'expenses' => '1150',
        ];
        return $this->fetchAccountNameByCode($vatCodes[$type] ?? null);
    }

    private function fetchAccountNameByCode($code)
    {
        if (empty($code)) return 'ไม่ระบุรหัสบัญชี';

        $cleanCode = trim($code);
        static $accountCache = [];
        if (isset($accountCache[$cleanCode])) return $accountCache[$cleanCode];

        $account = ChartOfAccount::where('code', $cleanCode)
            ->orWhere('code', 'like', $cleanCode . '%')
            ->first();

        if ($account) {
            $name = $account->name_th ?: ($account->name ?? 'ไม่มีชื่อบัญชี');
            $result = "{$account->code} - {$name}";
        } else {
            $result = "{$cleanCode} - (ไม่พบในผังบัญชี)";
        }

        $accountCache[$cleanCode] = $result;
        return $result;
    }
}
