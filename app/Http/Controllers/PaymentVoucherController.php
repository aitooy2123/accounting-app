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
    public function index(Request $request)
    {
        $query = PaymentVoucher::with(['items.account', 'payee']);

        // =========================
        // SEARCH
        // =========================

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // ค้นหาเลขที่ PV
                $q->where('pv_no', 'like', "%{$search}%")

                    // ค้นหา note
                    ->orWhere('note', 'like', "%{$search}%")

                    // ค้นหาชื่อผู้รับเงิน
                    ->orWhereHas('payee', function ($payeeQuery) use ($search) {
                        $payeeQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // =========================
        // DATE FILTER
        // =========================

        if ($request->filled('date_from')) {
            $query->whereDate('pv_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pv_date', '<=', $request->date_to);
        }

        // =========================
        // SORT + PAGINATE
        // =========================

        $vouchers = $query
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return view('pages.pv.index', compact('vouchers'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::all();
        $payees = Payee::all();

        return view('pages.pv.create', compact('accounts', 'payees'));
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

        return redirect()
            ->route('pv.index')
            ->with('success', 'บันทึก PV สำเร็จ');
    }

    public function show($id)
    {
        $pv = PaymentVoucher::with([
            'items.account',
            'payee'
        ])->findOrFail($id);

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
