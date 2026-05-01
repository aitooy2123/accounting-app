<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        // เพิ่ม Filter การค้นหาและสถานะเพื่อให้สอดคล้องกับหน้า Index ที่มีตัวกรอง
        $query = Branch::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('manager', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $branches = $query->orderBy('code', 'asc')->paginate(10);

        return view('pages.branch.index', compact('branches'));
    }

    public function create()
    {
        // --- Logic สำหรับรันรหัส BR-0001 อัตโนมัติ ---
        $lastBranch = Branch::orderBy('id', 'desc')->first();

        if (!$lastBranch) {
            $autoCode = 'BR-0001';
        } else {
            // ดึงเฉพาะตัวเลขออกมาจาก BR-XXXX
            $lastNumber = (int) str_replace('BR-', '', $lastBranch->code);
            $nextNumber = $lastNumber + 1;
            $autoCode = 'BR-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return view('pages.branch.create', compact('autoCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:branches',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'manager' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // จัดการค่า checkbox (ถ้าไม่ได้ติ๊กให้เป็น false)
        $validated['is_active'] = $request->has('is_active');

        Branch::create($validated);

        return redirect()->route('branches.index')
            ->with('success', 'เพิ่มข้อมูลสาขาใหม่เรียบร้อยแล้ว');
    }

    public function edit(Branch $branch)
    {
        return view('pages.branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'manager' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $branch->update($validated);

        return redirect()->route('branches.index')
            ->with('success', 'อัปเดตข้อมูลสาขาเรียบร้อยแล้ว');
    }

    public function destroy(Branch $branch)
    {
        try {
            $branch->delete();
            return redirect()->route('branches.index')
                ->with('success', 'ลบข้อมูลสาขาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->route('branches.index')
                ->with('error', 'ไม่สามารถลบข้อมูลนี้ได้ เนื่องจากมีการใช้งานอยู่ในส่วนอื่น');
        }
    }
}
