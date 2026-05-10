<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * ชื่อตารางในฐานข้อมูล
     */
    protected $table = 'quotations';

    /**
     * ฟิลด์ที่สามารถกรอกข้อมูลได้ (Mass Assignment)
     */
    protected $fillable = [
        // ข้อมูลเอกสาร
        'quotation_number',
        'reference_number',
        'quotation_date',
        'expiry_date',
        'status',

        // ข้อมูลผู้ขาย (Seller/Company)
        'seller_company_name',
        'seller_tax_id',
        'seller_address',
        'seller_phone',
        'seller_email',
        'seller_contact_person',
        'seller_branch',

        // ข้อมูลผู้ซื้อ (Buyer/Customer)
        'customer_id',
        'buyer_company_name',
        'buyer_tax_id',
        'buyer_address',
        'buyer_phone',
        'buyer_email',
        'buyer_contact_person',
        'buyer_project_name',

        // ข้อมูลทางการเงิน
        'subtotal',
        'discount_type',      // 'fixed' หรือ 'percentage'
        'discount_value',
        'discount_amount',
        'vat_rate',
        'vat_amount',
        'withholding_tax_rate',
        'withholding_tax_amount',
        'grand_total',

        // เงื่อนไข
        'credit_terms',
        'credit_days',
        'delivery_terms',
        'delivery_date',
        'warranty_period',

        // เพิ่มเติม
        'notes',
        'terms_conditions',
        'branch_id',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * การแปลงประเภทข้อมูล (Casting)
     */
    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'withholding_tax_rate' => 'decimal:2',
        'withholding_tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * สถานะของใบเสนอราคา
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CONVERTED = 'converted';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * รายการสถานะทั้งหมด
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'แบบร่าง',
            self::STATUS_SENT => 'ส่งให้ลูกค้าแล้ว',
            self::STATUS_APPROVED => 'อนุมัติแล้ว',
            self::STATUS_REJECTED => 'ลูกค้าปฏิเสธ',
            self::STATUS_EXPIRED => 'หมดอายุ',
            self::STATUS_CONVERTED => 'แปลงเป็นใบแจ้งหนี้แล้ว',
            self::STATUS_CANCELLED => 'ยกเลิกแล้ว',
        ];
    }

    /**
     * ดึงชื่อสถานะภาษาไทย
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * ตรวจสอบว่าสามารถแก้ไขได้หรือไม่
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_SENT,
        ]);
    }

    /**
     * ตรวจสอบว่าสามารถแปลงเป็นใบแจ้งหนี้ได้หรือไม่
     */
    public function isConvertible(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
        ]);
    }

    /**
     * ตรวจสอบว่าใบเสนอราคาหมดอายุหรือยัง
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && Carbon::parse($this->expiry_date)->isPast();
    }

    /**
     * ความสัมพันธ์กับ Customer (ถ้ามีตาราง customers)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * ความสัมพันธ์กับ QuotationItem (รายการสินค้า/บริการ)
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * ความสัมพันธ์กับผู้สร้าง
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ความสัมพันธ์กับผู้อนุมัติ
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * ความสัมพันธ์กับ Invoice ที่แปลงจากใบเสนอราคานี้
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scope สำหรับกรองตามสถานะ
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope สำหรับกรองใบเสนอราคาที่ยังไม่หมดอายุ
     */
    public function scopeActive($query)
    {
        return $query->where('expiry_date', '>=', now());
    }

    /**
     * Scope สำหรับกรองใบเสนอราคาที่เลยกำหนด
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope สำหรับค้นหาตามเลขที่เอกสารหรือชื่อลูกค้า
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('quotation_number', 'like', "%{$keyword}%")
              ->orWhere('buyer_company_name', 'like', "%{$keyword}%")
              ->orWhere('buyer_contact_person', 'like', "%{$keyword}%")
              ->orWhere('reference_number', 'like', "%{$keyword}%");
        });
    }

    /**
     * Boot model
     */
    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            // สร้างเลขที่เอกสารอัตโนมัติ (ถ้ายังไม่มี)
            if (empty($quotation->quotation_number)) {
                $quotation->quotation_number = self::generateQuotationNumber();
            }

            // กำหนดวันที่ออกเอกสาร (ถ้ายังไม่มี)
            if (empty($quotation->quotation_date)) {
                $quotation->quotation_date = now();
            }

            // กำหนดวันหมดอายุเริ่มต้น (15 วัน)
            if (empty($quotation->expiry_date)) {
                $quotation->expiry_date = Carbon::parse($quotation->quotation_date)->addDays(15);
            }
        });
    }

    /**
     * สร้างเลขที่ใบเสนอราคา
     * รูปแบบ: QT-ปี พ.ศ.-รันนิง 4 หลัก
     */
    public static function generateQuotationNumber(): string
    {
        $year = now()->year + 543; // แปลง ค.ศ. เป็น พ.ศ.
        $prefix = "QT-{$year}-";

        // หาเลขที่ล่าสุดของปีนี้
        $lastQuotation = self::where('quotation_number', 'like', "QT-{$year}-%")
            ->withTrashed() // นับรวมที่ถูกลบด้วยเพื่อกันเลขซ้ำ
            ->orderBy('quotation_number', 'desc')
            ->first();

        if ($lastQuotation) {
            // ดึงเลขรันนิง 4 หลักท้ายสุด
            $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
