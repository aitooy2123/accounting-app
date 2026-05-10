<?php

namespace App\Services\Sales;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QuotationService
{
    /**
     * ดึงรายการใบเสนอราคาทั้งหมด พร้อมตัวกรอง
     */
    public function getQuotations(
        string $keyword = null,
        string $status = null,
        string $dateFrom = null,
        string $dateTo = null,
        string $sortBy = 'quotation_date',
        string $sortOrder = 'desc',
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Quotation::with(['customer', 'creator', 'items']);

        // ค้นหาจาก keyword
        if ($keyword) {
            $query->search($keyword);
        }

        // กรองตามสถานะ
        if ($status) {
            $query->status($status);
        }

        // กรองตามช่วงวันที่
        if ($dateFrom) {
            $query->whereDate('quotation_date', '>=', Carbon::parse($dateFrom));
        }
        if ($dateTo) {
            $query->whereDate('quotation_date', '<=', Carbon::parse($dateTo));
        }

        // เรียงลำดับ
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * สร้างใบเสนอราคาใหม่
     */
    public function createQuotation(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            // สร้างใบเสนอราคา
            $quotation = Quotation::create([
                'seller_company_name' => $data['seller_company_name'] ?? config('company.name'),
                'seller_tax_id' => $data['seller_tax_id'] ?? config('company.tax_id'),
                'seller_address' => $data['seller_address'] ?? config('company.address'),
                'seller_phone' => $data['seller_phone'] ?? config('company.phone'),
                'seller_email' => $data['seller_email'] ?? config('company.email'),

                'customer_id' => $data['customer_id'] ?? null,
                'buyer_company_name' => $data['buyer_company_name'],
                'buyer_tax_id' => $data['buyer_tax_id'] ?? null,
                'buyer_address' => $data['buyer_address'] ?? null,
                'buyer_phone' => $data['buyer_phone'] ?? null,
                'buyer_email' => $data['buyer_email'] ?? null,
                'buyer_contact_person' => $data['buyer_contact_person'] ?? null,
                'buyer_project_name' => $data['buyer_project_name'] ?? null,

                'quotation_date' => $data['quotation_date'] ?? now(),
                'expiry_date' => $data['expiry_date'] ?? Carbon::now()->addDays(15),
                'credit_terms' => $data['credit_terms'] ?? null,
                'credit_days' => $data['credit_days'] ?? 0,
                'delivery_terms' => $data['delivery_terms'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,

                'vat_rate' => $data['vat_rate'] ?? 7,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_value' => $data['discount_value'] ?? 0,

                'notes' => $data['notes'] ?? null,
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // เพิ่มรายการสินค้า/บริการ
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $index => $itemData) {
                    $quotation->items()->create([
                        'line_number' => $index + 1,
                        'product_id' => $itemData['product_id'] ?? null,
                        'product_code' => $itemData['product_code'] ?? null,
                        'description' => $itemData['description'],
                        'specification' => $itemData['specification'] ?? null,
                        'quantity' => $itemData['quantity'] ?? 1,
                        'unit' => $itemData['unit'] ?? 'หน่วย',
                        'unit_price' => $itemData['unit_price'] ?? 0,
                        'discount_type' => $itemData['discount_type'] ?? null,
                        'discount_value' => $itemData['discount_value'] ?? 0,
                        'vat_rate' => $itemData['vat_rate'] ?? $quotation->vat_rate,
                        'sort_order' => $index,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            // คำนวณยอดรวมใหม่
            $quotation->recalculateTotals();

            return $quotation->fresh(['items']);
        });
    }

    /**
     * อัปเดตใบเสนอราคา
     */
    public function updateQuotation(Quotation $quotation, array $data): Quotation
    {
        if (!$quotation->isEditable()) {
            throw new \Exception('ไม่สามารถแก้ไขใบเสนอราคาที่มีสถานะ ' . $quotation->status_label . ' ได้');
        }

        return DB::transaction(function () use ($quotation, $data) {
            // อัปเดตข้อมูลหลัก
            $quotation->update([
                'buyer_company_name' => $data['buyer_company_name'] ?? $quotation->buyer_company_name,
                'buyer_tax_id' => $data['buyer_tax_id'] ?? $quotation->buyer_tax_id,
                'buyer_address' => $data['buyer_address'] ?? $quotation->buyer_address,
                'buyer_phone' => $data['buyer_phone'] ?? $quotation->buyer_phone,
                'buyer_email' => $data['buyer_email'] ?? $quotation->buyer_email,
                'buyer_contact_person' => $data['buyer_contact_person'] ?? $quotation->buyer_contact_person,
                'buyer_project_name' => $data['buyer_project_name'] ?? $quotation->buyer_project_name,

                'expiry_date' => $data['expiry_date'] ?? $quotation->expiry_date,
                'credit_terms' => $data['credit_terms'] ?? $quotation->credit_terms,
                'credit_days' => $data['credit_days'] ?? $quotation->credit_days,
                'delivery_terms' => $data['delivery_terms'] ?? $quotation->delivery_terms,
                'delivery_date' => $data['delivery_date'] ?? $quotation->delivery_date,

                'discount_type' => $data['discount_type'] ?? $quotation->discount_type,
                'discount_value' => $data['discount_value'] ?? $quotation->discount_value,

                'notes' => $data['notes'] ?? $quotation->notes,
                'terms_conditions' => $data['terms_conditions'] ?? $quotation->terms_conditions,
            ]);

            // อัปเดตรายการสินค้า (ลบเก่าแล้วเพิ่มใหม่)
            if (!empty($data['items']) && is_array($data['items'])) {
                $quotation->items()->delete();

                foreach ($data['items'] as $index => $itemData) {
                    $quotation->items()->create([
                        'line_number' => $index + 1,
                        'product_id' => $itemData['product_id'] ?? null,
                        'product_code' => $itemData['product_code'] ?? null,
                        'description' => $itemData['description'],
                        'specification' => $itemData['specification'] ?? null,
                        'quantity' => $itemData['quantity'] ?? 1,
                        'unit' => $itemData['unit'] ?? 'หน่วย',
                        'unit_price' => $itemData['unit_price'] ?? 0,
                        'discount_type' => $itemData['discount_type'] ?? null,
                        'discount_value' => $itemData['discount_value'] ?? 0,
                        'vat_rate' => $itemData['vat_rate'] ?? $quotation->vat_rate,
                        'sort_order' => $index,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            // คำนวณยอดรวมใหม่
            $quotation->recalculateTotals();

            return $quotation->fresh(['items']);
        });
    }

    /**
     * อัปเดตสถานะใบเสนอราคา
     */
    public function updateStatus(Quotation $quotation, string $status, int $approvedBy = null): Quotation
    {
        $validStatuses = array_keys(Quotation::getStatuses());

        if (!in_array($status, $validStatuses)) {
            throw new \Exception('สถานะไม่ถูกต้อง');
        }

        $data = ['status' => $status];

        if ($status === Quotation::STATUS_APPROVED) {
            $data['approved_by'] = $approvedBy ?? auth()->id();
            $data['approved_at'] = now();
        }

        $quotation->update($data);

        return $quotation;
    }

    /**
     * แปลงใบเสนอราคาเป็นใบแจ้งหนี้
     */
    public function convertToInvoice(Quotation $quotation): \App\Models\Invoice
    {
        if (!$quotation->isConvertible()) {
            throw new \Exception('ไม่สามารถแปลงใบเสนอราคาที่มีสถานะ ' . $quotation->status_label . ' ได้');
        }

        return DB::transaction(function () use ($quotation) {
            // สร้าง Invoice จาก Quotation
            $invoice = \App\Models\Invoice::create([
                'quotation_id' => $quotation->id,
                'customer_id' => $quotation->customer_id,
                'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
                'invoice_date' => now(),
                'due_date' => $quotation->credit_days > 0 ? Carbon::now()->addDays($quotation->credit_days) : now(),
                'buyer_company_name' => $quotation->buyer_company_name,
                'buyer_tax_id' => $quotation->buyer_tax_id,
                'buyer_address' => $quotation->buyer_address,
                'buyer_phone' => $quotation->buyer_phone,
                'buyer_email' => $quotation->buyer_email,
                'buyer_contact_person' => $quotation->buyer_contact_person,
                'subtotal' => $quotation->subtotal,
                'discount_amount' => $quotation->discount_amount,
                'vat_amount' => $quotation->vat_amount,
                'grand_total' => $quotation->grand_total,
                'credit_terms' => $quotation->credit_terms,
                'credit_days' => $quotation->credit_days,
                'notes' => $quotation->notes,
                'created_by' => auth()->id(),
                'status' => 'draft',
            ]);

            // คัดลอกรายการสินค้า
            foreach ($quotation->items as $item) {
                $invoice->items()->create([
                    'line_number' => $item->line_number,
                    'product_id' => $item->product_id,
                    'product_code' => $item->product_code,
                    'description' => $item->description,
                    'specification' => $item->specification,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount_amount' => $item->discount_amount,
                    'amount' => $item->amount,
                    'vat_amount' => $item->vat_amount,
                    'total_amount' => $item->total_amount,
                    'sort_order' => $item->sort_order,
                    'notes' => $item->notes,
                ]);
            }

            // อัปเดตสถานะ Quotation เป็น converted
            $quotation->update(['status' => Quotation::STATUS_CONVERTED]);

            return $invoice;
        });
    }

    /**
     * ลบใบเสนอราคา
     */
    public function deleteQuotation(Quotation $quotation, bool $force = false): bool
    {
        if ($force) {
            return $quotation->forceDelete();
        }

        // ตรวจสอบว่ามี Invoice ที่แปลงจากใบนี้หรือไม่
        if ($quotation->invoices()->exists()) {
            throw new \Exception('ไม่สามารถลบใบเสนอราคาที่ถูกแปลงเป็นใบแจ้งหนี้แล้ว');
        }

        return $quotation->delete();
    }

    /**
     * ดึงสถิติสำหรับ Dashboard
     */
    public function getDashboardStats(): array
    {
        $currentMonth = now()->startOfMonth();

        return [
            'total_quotations' => Quotation::count(),
            'total_this_month' => Quotation::where('created_at', '>=', $currentMonth)->count(),
            'total_amount' => Quotation::sum('grand_total'),
            'amount_this_month' => Quotation::where('created_at', '>=', $currentMonth)->sum('grand_total'),
            'status_breakdown' => Quotation::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'pending_approval' => Quotation::where('status', 'sent')->count(),
            'expiring_soon' => Quotation::whereIn('status', ['draft', 'sent'])
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', Carbon::now()->addDays(7))
                ->whereDate('expiry_date', '>=', Carbon::now())
                ->count(),
        ];
    }
}
