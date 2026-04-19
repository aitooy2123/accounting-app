<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;

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
        $query = Sale::query();

        // ค้นหาตามเลขที่เอกสาร
        if ($request->filled('search')) {
            $query->where('doc_no', 'like', '%' . $request->search . '%');
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ดึงข้อมูลและเรียงจากล่าสุด
        $sales = $query->orderBy('doc_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.sales', compact('sales'));
    }

    public function sales_destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $docNo = $sale->doc_no;
        $sale->delete(); // SaleItem จะโดนลบด้วยถ้าตั้ง onDelete('cascade') ใน Migration

        return redirect()->route('pages.sales')->with('success', "ลบเอกสาร $docNo เรียบร้อยแล้ว");
    }

    /**
     * Show the form for creating a new sale. (หน้าสร้างเอกสาร)
     */
    public function sales_create()
    {
        // จำลองข้อมูลลูกค้า (ในระบบจริงดึงจาก Model Customer)
        $customers = collect([
            (object)['id' => 1, 'name' => 'บริษัท เอบีซี จำกัด (มหาชน)', 'tax_id' => '0105560000001', 'address' => '123 ถ.วิภาวดีรังสิต แขวงจตุจักร เขตจตุจักร กทม. 10900'],
            (object)['id' => 2, 'name' => 'ร้านใจดี การค้า', 'tax_id' => '3100000000000', 'address' => '45/1 ม.2 ต.บางกรวย อ.บางกรวย จ.นนทบุรี 11130'],
            (object)['id' => 3, 'name' => 'บจก. ไทยโพสต์ (สำนักงานใหญ่)', 'tax_id' => '0105546000002', 'address' => '111 ถ.แจ้งวัฒนะ แขวงทุ่งสองห้อง เขตหลักสี่ กทม. 10210']
        ]);

        // จำลองข้อมูลสาขา
        $branches = collect([
            (object)['id' => 0, 'name' => 'สำนักงานใหญ่'],
            (object)['id' => 1, 'name' => 'สาขากรุงเทพฯ'],
            (object)['id' => 2, 'name' => 'สาขาเชียงใหม่'],
            (object)['id' => 3, 'name' => 'สาขาภูเก็ต']
        ]);

        return view('pages.sales_create', compact('customers', 'branches'));
    }

    public function sales_store(Request $request)
    {
        // ... (Validation เดิมของคุณ) ...
        // $validator = Validator::make($request->all(), [
        //     'customer_id' => 'required',
        //     'branch_id'   => 'required',
        //     'doc_date'    => 'required',
        //     'items'       => 'required|array',
        //     'items.*.product_id' => 'required',
        //     'items.*.qty' => 'required|numeric',
        //     'items.*.price' => 'required|numeric'
        // ]);


        try {
            DB::beginTransaction();

            // 1. คำนวณยอดเงิน
            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['qty'] * $item['price'];
            });
            $vat = $subtotal * 0.07;
            $total = $subtotal + $vat;

            // 2. บันทึกหัวเอกสาร (Sale)
            $sale = Sale::create([
                'doc_no'      => 'INV-' . now()->format('Ymd-His'),
                'customer_id' => $request->customer_id,
                'branch_id'   => $request->branch_id,
                'doc_date'    => $request->doc_date,
                'due_date'    => now()->parse($request->doc_date)->addDays($request->credit_term ?? 30),
                'subtotal'    => $subtotal,
                'vat'         => $vat,
                'total'       => $total,
                'note'        => $request->note,
                'status'      => 'ค้างชำระ'
            ]);

            // 3. บันทึกรายการสินค้า (SaleItems)
            // foreach ($request->items as $item) {
            //     $sale->items()->create([
            //         'description' => $item['desc'],
            //         'quantity'    => $item['qty'],
            //         'unit_price'  => $item['price'],
            //         'total'       => $item['qty'] * $item['price'],
            //     ]);
            // }

            DB::commit();
            return redirect()->route('pages.sales')->with('success', 'ออกใบกำกับภาษีเลขที่ ' . $sale->doc_no . ' เรียบร้อย');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
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

    // เมนูอื่นๆ
    public function customers()
    {
        return view('pages.customers');
    }
    public function company()
    {
        return view('pages.company');
    }
    public function branches()
    {
        return view('pages.branches');
    }
}
