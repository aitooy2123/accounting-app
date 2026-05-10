<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Sale;        // เพิ่มบรรทัดนี้
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ExcelService;
use App\Imports\CustomersImport; // If using Laravel Excel package

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        // กำหนดช่วงวันที่
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        $query = Customer::query()->with(['company', 'branch']);

        // ค้นหา
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('branch', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $customers = $query->orderBy('code', 'asc')->paginate(15);

        return view('pages.customer.index', compact(
            'customers',
            'startDate',
            'endDate'
        ));
    }

  public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        try {
            // ตรวจสอบ encoding ก่อน import
            $file = $request->file('file');
            $path = $file->getRealPath();

            // อ่านตัวอย่างข้อมูลเพื่อ debug
            $testData = Excel::toArray(new CustomersImport, $file);
            \Log::info('Import test data:', $testData);

            // ทำการ import จริง
            Excel::import(new CustomersImport, $file);

            return back()->with('success', 'นำเข้าข้อมูลสำเร็จ');

        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

public function downloadTemplate(ExcelService $excelService)
{
    return response()->streamDownload(function () use ($excelService) {
        $spreadsheet = $excelService->createCustomerTemplate();
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }, 'customer_template.xlsx');
}
    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        // ✅ Auto generate customer code
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        if ($lastCustomer && $lastCustomer->code) {
            $lastNumber = intval(substr($lastCustomer->code, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $autoCode = 'CUS-' . $newNumber;
        } else {
            $autoCode = 'CUS-00001';
        }

        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('pages.customer.create', compact('autoCode', 'companies', 'branches'));
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:customers,code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'เพิ่มลูกค้า ' . $validated['code'] . ' เรียบร้อยแล้ว');
    }

    /**
     * Display the specified customer.
     */
// ใน CustomerController.php หรือ Controller ที่ใช้แสดงหน้า show customer

// ใน CustomerController.php
// ใน CustomerController หรือ Controller ที่เรียกใช้ view นี้
public function show($id)
{
    $customer = Customer::findOrFail($id);

    $recentSales = Sale::where('customer_id', $customer->id)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();

    // เปลี่ยนจาก customer_id เป็น supplier_id
    $recentPurchases = Purchase::where('supplier_id', $customer->id)
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();

    return view('pages.customer.show', compact('customer', 'recentSales', 'recentPurchases'));
}
    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('pages.customer.edit', compact('customer', 'companies', 'branches'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:customers,code,' . $customer->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'อัปเดตข้อมูลลูกค้า ' . $customer->code . ' เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();

            // ตรวจสอบว่ามีรายการขายผูกอยู่หรือไม่
            if (method_exists($customer, 'sales') && $customer->sales()->count() > 0) {
                return back()->with('error', 'ไม่สามารถลบลูกค้าได้เนื่องจากมีเอกสารขายที่เกี่ยวข้องอยู่');
            }

            $customerCode = $customer->code;
            $customerName = $customer->name;
            $customer->delete();

            DB::commit();

            return redirect()
                ->route('customers.index')
                ->with('success', 'ลบลูกค้า ' . $customerCode . ' เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Toggle customer active status.
     */
    public function toggleStatus(Customer $customer, Request $request)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $customer->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตสถานะเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตสถานะได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete customers.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:customers,id'
        ], [
            'ids.required' => 'กรุณาเลือกลูกค้าอย่างน้อย 1 รายการ',
            'ids.min' => 'กรุณาเลือกลูกค้าอย่างน้อย 1 รายการ',
            'ids.*.exists' => 'ไม่พบลูกค้าที่เลือกในระบบ'
        ]);

        try {
            DB::beginTransaction();

            $customers = Customer::whereIn('id', $request->ids)->get();
            $deletedCount = 0;
            $failedCustomers = [];

            foreach ($customers as $customer) {
                // ตรวจสอบว่ามีเอกสารขายหรือไม่
                if (method_exists($customer, 'sales') && $customer->sales()->count() > 0) {
                    $failedCustomers[] = "{$customer->code}: มีเอกสารขายที่เกี่ยวข้อง";
                    continue;
                }

                $customer->delete();
                $deletedCount++;
            }

            DB::commit();

            if ($deletedCount > 0 && count($failedCustomers) === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "ลบข้อมูลลูกค้า {$deletedCount} รายการเรียบร้อยแล้ว",
                    'deleted_count' => $deletedCount
                ]);
            } elseif ($deletedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "ลบ {$deletedCount} รายการ แต่มี " . count($failedCustomers) . " รายการที่ไม่สามารถลบได้",
                    'deleted_count' => $deletedCount,
                    'failed' => $failedCustomers
                ]);
            } else {
                throw new \Exception("ไม่สามารถลบรายการที่เลือกได้: " . implode(', ', $failedCustomers));
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }



    }

}
