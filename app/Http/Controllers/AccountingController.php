<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AccountingController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        return view('dashboard', compact('title'));
    }

    public function banks()
    {
        $bankAccounts = [
            (object)[
                'bank_name' => 'Kasikorn Bank',
                'account_number' => '123-4-56789-0',
                'account_type' => 'ออมทรัพย์',
                'balance' => 1200500,
                'color_class' => 'bg-gradient-to-br from-green-600 to-green-700'
            ],
            (object)[
                'bank_name' => 'Siam Commercial Bank',
                'account_number' => '987-6-54321-0',
                'account_type' => 'กระแสรายวัน',
                'balance' => 250000,
                'color_class' => 'bg-gradient-to-br from-purple-700 to-purple-800'
            ]
        ];

        return view('pages.banks', compact('bankAccounts'));
    }

    /**
     * Display a listing of the sales. (หน้าแสดงรายการขาย)
     */
    public function sales(Request $request)
    {
        $allData = collect(range(1, 15))->map(function ($i) {
            $status = $i % 3 == 0 ? 'ค้างชำระ' : 'ชำระแล้ว';
            return (object)[
                'id' => $i,
                'doc_no' => 'INV202604' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer' => ($i % 2 == 0 ? 'บริษัท เอบีซี จำกัด' : 'ร้านใจดี การค้า') . ' ' . $i,
                'date' => now()->subDays($i)->format('d/m/Y'),
                'amount' => rand(5000, 50000),
                'status' => $status,
                'status_color' => $status == 'ค้างชำระ' ? 'text-red-600 bg-red-50' : 'text-green-600 bg-green-50'
            ];
        });

        $search = $request->get('search');
        $statusFilter = $request->get('status');

        $filteredData = $allData->filter(function ($item) use ($search, $statusFilter) {
            $matchSearch = true;
            $matchStatus = true;

            if ($search) {
                $matchSearch = str_contains($item->doc_no, $search) ||
                    str_contains(mb_strtolower($item->customer), mb_strtolower($search));
            }

            if ($statusFilter) {
                $matchStatus = $item->status == $statusFilter;
            }

            return $matchSearch && $matchStatus;
        });

        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $currentItems = $filteredData->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $sales = new LengthAwarePaginator(
            $currentItems,
            $filteredData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.sales', compact('sales'));
    }

    /**
     * Show the form for creating a new sale. (หน้าสร้างเอกสาร)
     */
    public function sales_create()
    {
        return view('pages.sales_create');
    }

    /**
     * Store a newly created sale in storage. (ฟังก์ชันบันทึกข้อมูล)
     */
    public function sales_store(Request $request)
    {
        // ในระบบจริง: BankAccount::create($request->all());
        return redirect()->route('sales')->with('success', 'บันทึกเอกสารเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified sale. (หน้าแก้ไขเอกสาร)
     */
    public function sales_edit($id)
    {
        // จำลองการดึงข้อมูลตาม ID
        $sale = (object)[
            'id' => $id,
            'doc_no' => 'INV202604' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'customer' => 'ร้านใจดี การค้า ' . $id,
            'amount' => 15000,
            'status' => 'ค้างชำระ'
        ];

        return view('pages.sales_edit', compact('sale'));
    }

    /**
     * Remove the specified sale from storage. (ฟังก์ชันลบข้อมูล)
     */
    public function sales_destroy($id)
    {
        // ในระบบจริง: BankAccount::destroy($id);
        return redirect()->route('sales')->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }

    // เมนูอื่นๆ
    public function customers() { return view('pages.customers'); }
    public function company() { return view('pages.company'); }
    public function branches() { return view('pages.branches'); }
}
