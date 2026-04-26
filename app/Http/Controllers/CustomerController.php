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
        $customers = Customer::with(['branch', 'company'])->orderBy('code')->paginate(10);
        return view('pages.customer.index', compact('customers'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        return view('pages.customer.create', compact('branches', 'companies'));
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
            ->with('success', 'เพิ่มลูกค้าเรียบร้อยแล้ว');
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
            ->with('success', 'อัปเดตลุกค้าเรียบร้อยแล้ว');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'ไม่สามารถลบลูกค้าได้เนื่องจากมีเอกสารขาย关联อยู่');
        }
        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'ลบลูกค้าเรียบร้อยแล้ว');
    }
}
