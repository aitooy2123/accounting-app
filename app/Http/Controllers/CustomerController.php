<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
    use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomersImport;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['branch', 'company'])
            ->when(request('search'), function($query) {
                $query->where('code', 'like', '%'.request('search').'%')
                      ->orWhere('name', 'like', '%'.request('search').'%')
                      ->orWhere('email', 'like', '%'.request('search').'%')
                      ->orWhere('phone', 'like', '%'.request('search').'%');
            })
            ->when(request('status') !== null, function($query) {
                $query->where('is_active', request('status'));
            })
            ->orderBy('code')
            ->paginate(10);

        return view('pages.customer.index', compact('customers'));
    }

    public function create()
    {
        // Auto generate customer code
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        if ($lastCustomer && $lastCustomer->code) {
            // Extract number from code (e.g., CUS-00001 -> 00001)
            $lastNumber = intval(substr($lastCustomer->code, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $autoCode = 'CUS-' . $newNumber;
        } else {
            $autoCode = 'CUS-00001';
        }

        $branches = Branch::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('pages.customer.create', compact('autoCode', 'branches', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'          => 'required|string|max:20|unique:customers',
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'tax_id'        => 'nullable|string|max:50',
            'contact_person'=> 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'is_active'     => 'boolean',
            'branch_id'     => 'nullable|exists:branches,id',
            'company_id'    => 'nullable|exists:companies,id',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'เพิ่มลูกค้า "' . $request->name . '" เรียบร้อยแล้ว');
    }

    public function edit(Customer $customer)
    {
        $branches = Branch::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        return view('pages.customer.edit', compact('customer', 'branches', 'companies'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code'          => 'required|string|max:20|unique:customers,code,' . $customer->id,
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'tax_id'        => 'nullable|string|max:50',
            'contact_person'=> 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'is_active'     => 'boolean',
            'branch_id'     => 'nullable|exists:branches,id',
            'company_id'    => 'nullable|exists:companies,id',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'อัปเดตลูกค้า "' . $customer->name . '" เรียบร้อยแล้ว');
    }

    public function destroy(Customer $customer)
    {
        // Check if customer has related sales documents
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'ไม่สามารถลบลูกค้าได้เนื่องจากมีเอกสารขายที่เกี่ยวข้องอยู่');
        }

        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'ลบลูกค้า "' . $customerName . '" เรียบร้อยแล้ว');
    }


public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    try {
        Excel::import(new CustomersImport, $request->file('file'));

        return back()->with('success', 'นำเข้าข้อมูลสำเร็จ');
    } catch (\Exception $e) {
        return back()->with('error', 'Import ไม่สำเร็จ: ' . $e->getMessage());
    }
}



public function downloadTemplate()
{
    $data = [
        ['name', 'phone', 'email', 'tax_id'],
        ['บริษัท ตัวอย่าง จำกัด', '0812345678', 'test@example.com', '0105551234567'],
    ];

    return Excel::download(
        new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        },
        'customer_import_template.xlsx'
    );
}
}
