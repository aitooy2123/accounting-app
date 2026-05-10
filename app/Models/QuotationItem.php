<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * ชื่อตารางในฐานข้อมูล
     */
    protected $table = 'quotation_items';

    /**
     * ฟิลด์ที่สามารถกรอกข้อมูลได้ (Mass Assignment)
     */
    protected $fillable = [
        'quotation_id',
        'line_number',
        'product_id',
        'product_code',
        'description',
        'specification',
        'quantity',
        'unit',
        'unit_price',
        'discount_type',      // 'fixed' หรือ 'percentage'
        'discount_value',
        'discount_amount',
        'amount',             // quantity * unit_price
        'net_amount',         // amount - discount_amount
        'vat_rate',
        'vat_amount',
        'total_amount',       // net_amount + vat_amount
        'sort_order',
        'notes',
    ];

    /**
     * การแปลงประเภทข้อมูล (Casting)
     */
    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * ความสัมพันธ์กับ Quotation (ใบเสนอราคา)
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * ความสัมพันธ์กับ Product (สินค้าในสต็อก - ถ้ามี)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * คำนวณจำนวนเงินก่อนส่วนลด
     */
    public function calculateAmount(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * คำนวณส่วนลด
     */
    public function calculateDiscount(): float
    {
        if ($this->discount_type === 'percentage') {
            return ($this->calculateAmount() * $this->discount_value) / 100;
        }

        return $this->discount_value ?? 0;
    }

    /**
     * คำนวณจำนวนเงินสุทธิหลังหักส่วนลด
     */
    public function calculateNetAmount(): float
    {
        return $this->calculateAmount() - $this->calculateDiscount();
    }

    /**
     * คำนวณภาษีมูลค่าเพิ่ม
     */
    public function calculateVat(): float
    {
        return ($this->calculateNetAmount() * $this->vat_rate) / 100;
    }

    /**
     * คำนวณยอดรวมทั้งสิ้น (สุทธิ + VAT)
     */
    public function calculateTotal(): float
    {
        return $this->calculateNetAmount() + $this->calculateVat();
    }

    /**
     * อัปเดตการคำนวณทั้งหมดและบันทึก
     */
    public function recalculate(): void
    {
        $this->amount = $this->calculateAmount();
        $this->discount_amount = $this->calculateDiscount();
        $this->net_amount = $this->calculateNetAmount();
        $this->vat_amount = $this->calculateVat();
        $this->total_amount = $this->calculateTotal();
        $this->save();
    }

    /**
     * Boot model
     */
    protected static function booted(): void
    {
        // เมื่อสร้างรายการใหม่ ให้คำนวณยอดเงินอัตโนมัติ
        static::saving(function (QuotationItem $item) {
            $item->amount = $item->calculateAmount();
            $item->discount_amount = $item->calculateDiscount();
            $item->net_amount = $item->calculateNetAmount();
            $item->vat_amount = $item->calculateVat();
            $item->total_amount = $item->calculateTotal();
        });

        // หลังจากบันทึก ให้อัปเดตยอดรวมในใบเสนอราคา
        static::saved(function (QuotationItem $item) {
            if ($item->quotation) {
                $item->quotation->recalculateTotals();
            }
        });

        // หลังจากลบ ให้อัปเดตยอดรวมในใบเสนอราคา
        static::deleted(function (QuotationItem $item) {
            if ($item->quotation) {
                $item->quotation->recalculateTotals();
            }
        });
    }
}
