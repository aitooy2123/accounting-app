<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Customer;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function journal(Request $request)
    {
        // 1. รับค่าจาก Filter
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $journalType = $request->get('journal_type', 'sales');
        $customerId = $request->get('customer_id');

        $rawItems = collect();

        // 2. ดึงข้อมูลตามประเภทสมุดรายวัน
        if ($journalType == 'sales') {
            $query = Sale::with(['customer'])
                ->whereBetween('doc_date', [$startDate, $endDate])
                ->where('status', '!=', 'ยกเลิก');

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            $rawItems = $query->get();

        } elseif ($journalType == 'purchase') {
            $rawItems = Purchase::with('supplier')
                ->whereBetween('doc_date', [$startDate, $endDate])->get();

        } elseif ($journalType == 'payment') {
            $rawItems = Payment::with('supplier')
                ->whereBetween('pay_date', [$startDate, $endDate])->get();

        } elseif ($journalType == 'receipt') {
            $rawItems = Receipt::with('customer')
                ->whereBetween('receipt_date', [$startDate, $endDate])->get();
        }

        // 3. แปลงข้อมูลเป็นรูปแบบ Journal Object
        $transactions = $rawItems->map(function ($item) use ($journalType) {
            return $this->formatTransaction($item, $journalType);
        })->sortBy('date');

        // คำนวณยอดรวม (สำหรับแสดงท้ายรายงาน)
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
            'journalType',
            'startDate',
            'endDate'
        ));
    }

    private function formatTransaction($item, $type)
    {
        // กำหนดชื่อคู่ค้า
        if (in_array($type, ['sales', 'receipt'])) {
            $partyName = $item->customer->name ?? 'ลูกค้าทั่วไป';
        } else {
            $partyName = $item->supplier->name ?? 'ผู้จำหน่ายทั่วไป';
        }

        // จัดการเรื่องตัวเลข
        $total = (float)($item->total ?? ($item->amount ?? 0));
        $vat = (float)($item->vat ?? 0);
        $subtotal = $total - $vat;

        return (object)[
            'date'           => $item->doc_date ?? ($item->pay_date ?? ($item->receipt_date ?? now())),
            'doc_no'         => $item->doc_no ?? ($item->reference_no ?? 'ERR-NO'),
            'customer_name'  => $partyName,
            'subtotal'       => $subtotal,
            'vat'            => $vat,
            'vat_rate'       => $item->vat_rate ?? 7,
            'total'          => $total,
            'debit_account'  => $this->getAccountMapping($type, 'dr'),
            'credit_account' => $this->getAccountMapping($type, 'cr'),
            'vat_account'    => $this->getVatAccountMapping($type),
            'rowspan'        => ($vat > 0) ? 3 : 2, // ใช้สำหรับจัดการตารางใน View
        ];
    }

    private function getAccountMapping($type, $side)
    {
        // ผังบัญชีเบื้องต้น (สามารถปรับเปลี่ยนรหัสให้ตรงกับ DB ของคุณได้ที่นี่)
        $mapping = [
            'sales'    => ['dr' => '1130', 'cr' => '4110'], // ลูกหนี้ / รายได้
            'purchase' => ['dr' => '5110', 'cr' => '2130'], // ซื้อสินค้า / เจ้าหนี้
            'payment'  => ['dr' => '2130', 'cr' => '1110'], // เจ้าหนี้ / เงินสด-ธนาคาร
            'receipt'  => ['dr' => '1110', 'cr' => '1130'], // เงินสด-ธนาคาร / ลูกหนี้
        ];

        $code = $mapping[$type][$side] ?? null;
        return $this->fetchAccountNameByCode($code);
    }

    private function getVatAccountMapping($type)
    {
        $vatCodes = [
            'sales'    => '2150', // ภาษีขาย
            'purchase' => '1150', // ภาษีซื้อ
        ];

        return $this->fetchAccountNameByCode($vatCodes[$type] ?? null);
    }

    /**
     * ดึงรหัสและชื่อบัญชีภาษาไทยจากฐานข้อมูล
     */
      private function fetchAccountNameByCode($code)
{
    if (empty($code)) return 'ไม่ระบุรหัส';

    $cleanCode = trim($code);
    static $accountCache = [];

    if (!isset($accountCache[$cleanCode])) {
        // 1. ลองหาแบบตรงตัวก่อน (เช่น '4100-01')
        $account = ChartOfAccount::where('code', $cleanCode)->first();

        // 2. ถ้าไม่เจอ และไม่มีขีดในคำค้นหา ให้ลองหาแบบ prefix (เช่น หา '4100' ใน '4100-01')
        if (!$account && strpos($cleanCode, '-') === false) {
            $account = ChartOfAccount::where('code', 'like', $cleanCode . '-%')->first();
        }

        if ($account) {
            $displayName = $account->name_th ?: ($account->name ?? 'ไม่มีชื่อบัญชี');
            $accountCache[$cleanCode] = "{$account->code} - {$displayName}";
        } else {
            // คืนค่ารหัสเดิมเพื่อให้รู้ว่าตัวไหนที่หาไม่เจอ
            $accountCache[$cleanCode] = "{$cleanCode} - (ไม่พบชื่อในผังบัญชี)";
        }
    }

    return $accountCache[$cleanCode];
}
}
