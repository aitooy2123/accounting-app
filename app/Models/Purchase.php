<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchases';

    protected $fillable = [
        'doc_no',
        'supplier_id',
        'branch_id',
        'doc_date',
        'due_date',
        'subtotal',
        'vat',
        'total',
        'vat_rate',
        'note',
        'status',
        // 'customer_id', // โดยปกติใช้ supplier_id แทน แนะนำให้เลือกอย่างใดอย่างหนึ่ง
    ];

    protected $casts = [
        'doc_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'vat' => 'decimal:2',
        'total' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    // --- Relationships ---

    /**
     * ดึงรายการสินค้าทั้งหมดในใบซื้อนี้
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    /**
     * ดึงข้อมูลผู้จำหน่าย (Supplier)
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'supplier_id');
    }

    /**
     * ดึงข้อมูลสาขา
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // --- Scopes สำหรับ Query ---

    public function scopePaid($query)
    {
        return $query->where('status', 'ชำระแล้ว');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'ค้างชำระ');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('doc_no', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    // --- Logic การสร้างเลขที่เอกสาร ---

    public static function generateDocNo(): string
    {
        $prefix = 'PO-' . date('Ym') . '-';
        $lastPurchase = self::where('doc_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPurchase) {
            $lastNumber = (int) substr($lastPurchase->doc_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
