<?php


namespace App\Http\Controllers;

use App\Models\Customer; // อย่าลืมเรียกใช้ Model Customer
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // สมุดรายวันทั่วไป
    public function journal($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);
        // เขียน Logic ดึงข้อมูลบัญชีตรงนี้ เช่น:
        // $transactions = Transaction::where('customer_id', $customer_id)->get();

        return view('pages.reports.journal', compact('customer'));
    }

    // แยกประเภท
    public function ledger($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);
        return view('pages.reports.ledger', compact('customer'));
    }

    // งบทดลอง
    public function trialBalance($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);
        return view('pages.reports.trial_balance', compact('customer'));
    }

    // งบกำไรขาดทุน
    public function pnl($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);
        return view('pages.reports.pnl', compact('customer'));
    }
}
