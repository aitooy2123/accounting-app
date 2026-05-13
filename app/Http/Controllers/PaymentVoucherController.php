<?php
namespace App\Http\Controllers;

use App\Models\PaymentVoucher;
use App\Models\PaymentVoucherItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;
use App\Models\Payee;

class PaymentVoucherController extends Controller
{
    public function index()
    {
        $vouchers = PaymentVoucher::with('items.account')
            ->latest()
            ->paginate(20);

        return view('pages.pv.index', compact('vouchers'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::all();

        return view('pages.pv.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pv_date' => 'required|date',
            'items' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {

            $pv = PaymentVoucher::create([
                'pv_no' => 'PV-' . date('YmdHis'),
                'pv_date' => $request->pv_date,
                'payee_id' => $request->payee_id ?? null,
                'note' => $request->note,
                'total_amount' => collect($request->items)->sum('amount'),
            ]);

            foreach ($request->items as $item) {
                PaymentVoucherItem::create([
                    'payment_voucher_id' => $pv->id,
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'type' => $item['type'],
                    'amount' => $item['amount'],
                    'description' => $item['description'] ?? null,
                ]);
            }
        });

        return redirect()->route('pv.index')
            ->with('success', 'บันทึก PV สำเร็จ');
    }

    public function show($id)
    {
        $pv = PaymentVoucher::with('items')->findOrFail($id);

        return view('pages.pv.show', compact('pv'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || count($ids) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูล'
            ]);
        }

        PaymentVoucher::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลเรียบร้อย'
        ]);
    }
}
