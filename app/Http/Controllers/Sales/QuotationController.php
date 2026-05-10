<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Services\Sales\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(
        private QuotationService $quotationService
    ) {}

    /**
     * แสดงรายการใบเสนอราคาทั้งหมด
     */
    public function index(Request $request): View
    {
        $quotations = $this->quotationService->getQuotations(
            keyword: $request->input('search'),
            status: $request->input('status'),
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
            sortBy: $request->input('sort_by', 'quotation_date'),
            sortOrder: $request->input('sort_order', 'desc'),
            perPage: $request->input('per_page', 15)
        );

        // ข้อมูลสำหรับฟิลเตอร์
        $statuses = Quotation::getStatuses();

        return view('sales.quotations.index', compact('quotations', 'statuses'));
    }

    /**
     * แสดงฟอร์มสร้างใบเสนอราคาใหม่
     */
    public function create(): View
    {
        return view('sales.quotations.form', [
            'quotation' => null,
            'companyInfo' => [
                'name' => config('company.name'),
                'tax_id' => config('company.tax_id'),
                'address' => config('company.address'),
                'phone' => config('company.phone'),
                'email' => config('company.email'),
            ],
        ]);
    }

    /**
     * บันทึกใบเสนอราคาใหม่
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'buyer_company_name' => 'required|string|max:255',
            'buyer_tax_id' => 'nullable|string|max:30',
            'buyer_address' => 'nullable|string|max:500',
            'buyer_phone' => 'nullable|string|max:30',
            'buyer_email' => 'nullable|email|max:100',
            'buyer_contact_person' => 'nullable|string|max:100',
            'buyer_project_name' => 'nullable|string|max:200',
            'customer_id' => 'nullable|exists:customers,id',

            'quotation_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:quotation_date',
            'credit_terms' => 'nullable|string|max:200',
            'credit_days' => 'nullable|integer|min:0',
            'delivery_terms' => 'nullable|string|max:500',
            'delivery_date' => 'nullable|date|after_or_equal:quotation_date',

            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit' => 'required|string|max:30',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',

            'notes' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:2000',
        ], [
            'buyer_company_name.required' => 'กรุณาระบุชื่อบริษัท/ลูกค้า',
            'items.required' => 'กรุณาเพิ่มรายการสินค้าหรือบริการอย่างน้อย 1 รายการ',
            'items.*.description.required' => 'กรุณาระบุชื่อสินค้า/บริการ',
            'items.*.quantity.min' => 'จำนวนต้องมากกว่า 0',
            'items.*.unit_price.min' => 'ราคาต่อหน่วยต้องมากกว่า 0',
        ]);

        try {
            $quotation = $this->quotationService->createQuotation($validated);

            return redirect()
                ->route('sales.quotations.show', $quotation)
                ->with('success', 'สร้างใบเสนอราคา ' . $quotation->quotation_number . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * แสดงรายละเอียดใบเสนอราคา
     */
    public function show(Quotation $quotation): View
    {
        $quotation->load(['customer', 'items', 'creator', 'approver', 'invoices']);

        return view('sales.quotations.show', compact('quotation'));
    }

    /**
     * แสดงฟอร์มแก้ไขใบเสนอราคา
     */
    public function edit(Quotation $quotation): View
    {
        if (!$quotation->isEditable()) {
            return redirect()
                ->route('sales.quotations.show', $quotation)
                ->with('error', 'ไม่สามารถแก้ไขใบเสนอราคาที่มีสถานะ ' . $quotation->status_label . ' ได้');
        }

        $quotation->load('items');

        return view('sales.quotations.form', compact('quotation'));
    }

    /**
     * อัปเดตใบเสนอราคา
     */
    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        if (!$quotation->isEditable()) {
            return redirect()
                ->route('sales.quotations.show', $quotation)
                ->with('error', 'ไม่สามารถแก้ไขใบเสนอราคาที่มีสถานะ ' . $quotation->status_label . ' ได้');
        }

        $validated = $request->validate([
            'buyer_company_name' => 'required|string|max:255',
            'buyer_tax_id' => 'nullable|string|max:30',
            'buyer_address' => 'nullable|string|max:500',
            'buyer_phone' => 'nullable|string|max:30',
            'buyer_email' => 'nullable|email|max:100',
            'buyer_contact_person' => 'nullable|string|max:100',
            'buyer_project_name' => 'nullable|string|max:200',

            'expiry_date' => 'nullable|date',
            'credit_terms' => 'nullable|string|max:200',
            'credit_days' => 'nullable|integer|min:0',
            'delivery_date' => 'nullable|date',

            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit' => 'required|string|max:30',
            'items.*.unit_price' => 'required|numeric|min:0',

            'notes' => 'nullable|string|max:2000',
        ]);

        try {
            $quotation = $this->quotationService->updateQuotation($quotation, $validated);

            return redirect()
                ->route('sales.quotations.show', $quotation)
                ->with('success', 'อัปเดตใบเสนอราคา ' . $quotation->quotation_number . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ลบใบเสนอราคา
     */
    public function destroy(Quotation $quotation): RedirectResponse
    {
        try {
            $quotationNumber = $quotation->quotation_number;
            $this->quotationService->deleteQuotation($quotation);

            return redirect()
                ->route('sales.quotations.index')
                ->with('success', 'ลบใบเสนอราคา ' . $quotationNumber . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * แปลงใบเสนอราคาเป็นใบแจ้งหนี้
     */
    public function convertToInvoice(Quotation $quotation): RedirectResponse
    {
        try {
            $invoice = $this->quotationService->convertToInvoice($quotation);

            return redirect()
                ->route('sales.invoices.show', $invoice)
                ->with('success', 'แปลงใบเสนอราคา ' . $quotation->quotation_number . ' เป็นใบแจ้งหนี้ ' . $invoice->invoice_number . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
