<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();

        // 🔍 ค้นหา
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // ✅ filter สถานะ
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $companies = $query->latest()->paginate(10);

        return view('pages.company.index', compact('companies'));
    }

    public function create()
    {
        $autoCode = Company::generateCode();
        return view('pages.company.create', compact('autoCode'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        // เพิ่ม unique:companies,name เพื่อเช็คว่าห้ามซ้ำในตาราง companies คอลัมน์ name
        'name'    => 'required|string|max:255|unique:companies,name',
        'email'   => 'nullable|email',
        'phone'   => 'nullable|string',
        'address' => 'nullable|string',
        'tax_id'  => 'nullable|string|max:20',
    ], [
        // เพิ่ม Custom Error Message (Optional)
        'name.unique' => 'ชื่อบริษัทนี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น',
    ]);

    $validated['code'] = $this->generateCompanyCode();

    Company::create($validated);

    return redirect()->route('companies.index')->with('success', 'บันทึกข้อมูลบริษัทเรียบร้อยแล้ว');
}

private function generateCompanyCode()
{
    // Example: COM-0001
    $lastId = Company::max('id') ?? 0;
    return 'COM-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
}

    public function edit(Company $company)
    {
        return view('pages.company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($company->id) // ✅ กันซ้ำแต่ยกเว้นตัวเอง
            ],
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
        ]);

        $company->update($request->only([
            'name', 'email', 'phone', 'address', 'tax_id'
        ]));

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
    public function toggleStatus(Company $company, Request $request)
{
    $company->update(['is_active' => $request->is_active]);
    return response()->json(['success' => true]);
}

  public function bulkDelete(Request $request)
    {
        // Validate request
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:companies,id'
        ], [
            'ids.required' => 'กรุณาเลือกบริษัทอย่างน้อย 1 รายการ',
            'ids.array' => 'รูปแบบข้อมูลไม่ถูกต้อง',
            'ids.min' => 'กรุณาเลือกบริษัทอย่างน้อย 1 รายการ',
            'ids.*.exists' => 'ไม่พบบริษัทที่เลือกในระบบ'
        ]);

        try {
            DB::beginTransaction();

            // Get companies to delete
            $companies = Company::whereIn('id', $request->ids)->get();
            $count = $companies->count();

            // Optional: Check if companies can be deleted (e.g., no related records)
            foreach ($companies as $company) {
                // Add your business logic checks here
                // Example: Check if company has employees, projects, etc.
                // if ($company->employees()->exists()) {
                //     throw new \Exception("บริษัท {$company->name} มีพนักงานอยู่ ไม่สามารถลบได้");
                // }
            }

            // Delete companies
            Company::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "ลบบริษัท {$count} รายการเรียบร้อยแล้ว",
                'deleted_count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }
}
