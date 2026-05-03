<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index(Request $request)
    {
        $query = Company::query();

        // Search
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

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $companies = $query->latest()->paginate(10);

        return view('pages.company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        $autoCode = $this->generateCompanyCode();
        return view('pages.company.create', compact('autoCode'));
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255|unique:companies,name',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'tax_id'  => 'nullable|string|max:20',
        ], [
            'name.unique' => 'ชื่อบริษัทนี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        ]);

        $validated['code'] = $this->generateCompanyCode();
        $validated['is_active'] = true;

        Company::create($validated);

        return redirect()
            ->route('companies.index')
            ->with('success', 'บันทึกข้อมูลบริษัทเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        return view('pages.company.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($company->id)
            ],
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'tax_id'  => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ], [
            'name.unique' => 'ชื่อบริษัทนี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น',
        ]);

        $company->update($validated);

        return redirect()
            ->route('companies.index')
            ->with('success', 'อัปเดตข้อมูลบริษัทเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company)
    {
        try {
            // Check if company has related branches
            if ($company->branches()->exists()) {
                return redirect()
                    ->back()
                    ->with('error', "ไม่สามารถลบบริษัท {$company->name} ได้ เนื่องจากมีสาขาที่เกี่ยวข้อง");
            }

            // Check if company has related customers
            if ($company->customers()->exists()) {
                return redirect()
                    ->back()
                    ->with('error', "ไม่สามารถลบบริษัท {$company->name} ได้ เนื่องจากมีลูกค้าที่เกี่ยวข้อง");
            }

            $companyName = $company->name;
            $company->delete();

            return redirect()
                ->route('companies.index')
                ->with('success', "ลบบริษัท {$companyName} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการลบบริษัท: ' . $e->getMessage());
        }
    }

    /**
     * Toggle company active status.
     */
    public function toggleStatus(Company $company, Request $request)
    {
        try {
            $company->update([
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
     * Bulk delete companies.
     */
    public function bulkDelete(Request $request)
    {
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

            $companies = Company::whereIn('id', $request->ids)->get();
            $count = $companies->count();
            $failedCompanies = [];

            foreach ($companies as $company) {
                // Check if company has branches
                if ($company->branches()->exists()) {
                    $failedCompanies[] = "{$company->name}: มีสาขาที่เกี่ยวข้อง";
                    continue;
                }

                // Check if company has customers
                if ($company->customers()->exists()) {
                    $failedCompanies[] = "{$company->name}: มีลูกค้าที่เกี่ยวข้อง";
                    continue;
                }
            }

            // Delete only companies that passed the checks
            $deletableIds = $companies->filter(function ($company) use ($failedCompanies) {
                return !collect($failedCompanies)->contains(function ($failed) use ($company) {
                    return str_contains($failed, $company->name);
                });
            })->pluck('id');

            $deletedCount = Company::whereIn('id', $deletableIds)->delete();

            DB::commit();

            if ($deletedCount > 0 && count($failedCompanies) === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "ลบบริษัท {$deletedCount} รายการเรียบร้อยแล้ว",
                    'deleted_count' => $deletedCount
                ]);
            } elseif ($deletedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "ลบบริษัท {$deletedCount} รายการ แต่มี " . count($failedCompanies) . " รายการที่ไม่สามารถลบได้",
                    'deleted_count' => $deletedCount,
                    'failed' => $failedCompanies
                ]);
            } else {
                throw new \Exception("ไม่สามารถลบบริษัทที่เลือกได้: " . implode(', ', $failedCompanies));
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate company code (COM-XXXX).
     */
    private function generateCompanyCode(): string
    {
        $lastCompany = Company::orderBy('id', 'desc')->first();

        if ($lastCompany && $lastCompany->code) {
            $lastNumber = (int) substr($lastCompany->code, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'COM-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
