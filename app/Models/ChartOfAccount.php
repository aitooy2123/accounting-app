<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = ['code', 'name_th', 'name_en', 'category', 'parent_id', 'is_group', 'is_active'];

    // ความสัมพันธ์ดึงบัญชีแม่
    public function parent() {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    // ความสัมพันธ์ดึงบัญชีย่อย (ลูก)
    public function children() {
        return $this->hasMany(ChartOfAccount::class, 'parent_id')->orderBy('code');
    }

public function edit(Sale $sale)
{
    $customers = Customer::all();
    $branches = Branch::all();
    $chartOfAccounts = ChartOfAccount::all(); // 👈 add this line

    return view('pages.sale.edit', compact('sale', 'customers', 'branches', 'chartOfAccounts'));
}
}
