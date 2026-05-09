<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PurchaseExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithProperties
{
    protected $id;
    protected $itemCount = 0;
    protected $purchaseData;

    public function __construct($id) {
        $this->id = $id;
        // โหลดข้อมูลหลักและ Relationship ที่จำเป็นล่วงหน้า
        $this->purchaseData = Purchase::with(['supplier', 'branch', 'items'])->find($id);

        if ($this->purchaseData) {
            $this->itemCount = $this->purchaseData->items->count();
        }
    }

    public function properties(): array {
        return [
            'creator'        => 'Pride Archidecco Co., Ltd.',
            'title'          => 'Purchase Order - ' . ($this->purchaseData->doc_no ?? ''),
            'company'        => 'Pride Archidecco Co., Ltd.',
            'category'       => 'Purchase Order',
        ];
    }

    public function query() {
        // ใช้ Query เพื่อประสิทธิภาพ แต่กรองเฉพาะ ID ที่ระบุ
        return Purchase::where('id', $this->id)->with(['items']);
    }

    public function startCell(): string {
        return 'A7'; // เริ่มต้นตารางที่แถวที่ 7 เพื่อเว้นที่ว่างให้ Header
    }

    public function headings(): array {
        return [
            'ลำดับ',
            'รายละเอียดสินค้า (Description)',
            'จำนวน (Qty)',
            'ราคา/หน่วย (Price)',
            'หน่วย (Unit)',
            'จำนวนเงิน (Amount)'
        ];
    }

    /**
     * @param Purchase $purchase
     */
    public function map($purchase): array {
        $rows = [];

        foreach ($purchase->items as $index => $item) {
            $rows[] = [
                $index + 1,
                $item->desc,
                $item->qty,
                $item->price,
                $item->unit ?? 'หน่วย',
                $item->total,
            ];
        }

        // ส่วนสรุปยอดเงิน (Summary Section)
        $rows[] = ['', '', '', '', 'รวมเงิน (Sub Total):', $purchase->subtotal];
        $rows[] = ['', '', '', '', 'ภาษีมูลค่าเพิ่ม (VAT ' . (int)$purchase->vat_rate . '%):', $purchase->vat];
        $rows[] = ['', '', '', '', 'ยอดรวมทั้งสิ้น (Grand Total):', $purchase->total];

        return $rows;
    }

    public function columnFormats(): array {
        return [
            'C' => '#,##0',         // จำนวน
            'D' => '#,##0.00',      // ราคา/หน่วย
            'F' => '#,##0.00',      // จำนวนเงิน
        ];
    }

    public function styles(Worksheet $sheet) {
        $purchase = $this->purchaseData;
        if (!$purchase) return [];

        // 1. การตั้งค่าหน้ากระดาษ (Page Setup)
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setScale(95);

        // 2. ส่วนหัวเอกสาร (Document Header)
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'ใบสั่งซื้อ / PURCHASE ORDER');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '27AE60']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // 3. ข้อมูลคู่ค้าและเลขที่เอกสาร (Supplier & Doc Info)
        // ฝั่งซ้าย: ผู้จำหน่าย
        $sheet->setCellValue('A3', 'นามผู้จำหน่าย (Supplier): ' . ($purchase->supplier->name ?? '-'));
        $sheet->setCellValue('A4', 'ที่อยู่ (Address): ' . ($purchase->supplier->address ?? '-'));
        $sheet->setCellValue('A5', 'โทร (Tel): ' . ($purchase->supplier->phone ?? '-'));

        // ฝั่งขวา: ข้อมูลใบสั่งซื้อ
        $sheet->setCellValue('E3', 'เลขที่ (No.): ' . $purchase->doc_no);
        $sheet->setCellValue('E4', 'วันที่ (Date): ' . $purchase->doc_date->format('d/m/Y'));
        $sheet->getStyle('E3:E4')->getFont()->setBold(true);

        // 4. ตกแต่งตารางสินค้า (Table Styling)
        $headerRow = 7;
        $lastDataRow = $headerRow + $this->itemCount;
        $totalRow = $lastDataRow + 3; // รวมสรุปยอด 3 บรรทัด

        // สไตล์หัวตาราง (เหมือนในรูป image_feb032.png)
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // เส้นขอบตารางทั้งหมด
        $sheet->getStyle("A{$headerRow}:F{$totalRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ],
            ]
        ]);

        // จัดตำแหน่งเนื้อหา
        $sheet->getStyle("A8:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // ลำดับ
        $sheet->getStyle("C8:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // จำนวน
        $sheet->getStyle("F8:F{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);     // ยอดเงิน

        // 5. ส่วนสรุปยอดเงิน (Summary Styling)
        $summaryRange = "E" . ($lastDataRow + 1) . ":F{$totalRow}";
        $sheet->getStyle($summaryRange)->getFont()->setBold(true);
        $sheet->getStyle("E" . ($lastDataRow + 1) . ":E{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ไฮไลท์บรรทัดยอดรวมสุทธิ
        $sheet->getStyle("A{$totalRow}:F{$totalRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F6EF']]
        ]);

        // 6. ส่วนท้ายเอกสาร (Footer Signatures)
        $footerStart = $totalRow + 3;
        $sheet->setCellValue("A{$footerStart}", "____________________");
        $sheet->setCellValue("A" . ($footerStart + 1), "ผู้สั่งซื้อ / Authorized By");

        $sheet->setCellValue("E{$footerStart}", "____________________");
        $sheet->setCellValue("E" . ($footerStart + 1), "ผู้รับของ / Received By");

        $sheet->getStyle("A{$footerStart}:F" . ($footerStart + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
