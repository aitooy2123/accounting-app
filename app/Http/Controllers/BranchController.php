<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('code')->paginate(10);
        return view('pages.branch.index', compact('branches'));
    }

    public function create()
    {
        return view('pages.branch.create');
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
            'is_active' => 'boolean',
        ]);

        Branch::create($validated);
        return redirect()->route('branches.index')
            ->with('success', 'เพิ่มสาขาเรียบร้อยแล้ว');
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
            'is_active' => 'boolean',
        ]);

        $branch->update($validated);
        return redirect()->route('branches.index')
            ->with('success', 'อัปเดตสาขาเรียบร้อยแล้ว');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')
            ->with('success', 'ลบสาขาเรียบร้อยแล้ว');
    }
}
