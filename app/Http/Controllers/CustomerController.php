<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;

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
    try {
        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'ลบลูกค้าเรียบร้อยแล้ว');
    } catch (\Exception $e) {
        return redirect()->route('customers.index')
            ->with('error', 'ไม่สามารถลบลูกค้าได้: ' . $e->getMessage());
    }
}
}
