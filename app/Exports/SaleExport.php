<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SaleExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell
{
    protected $id;
    protected $itemCount = 0;
    protected $saleData;

    public function __construct($id) {
        $this->id = $id;
        // ดึงข้อมูลหลักเตรียมไว้สำหรับส่วนหัว
        $this->saleData = Sale::with(['customer', 'branch'])->find($id);
    }

    public function query() {
        return Sale::with(['items'])->where('id', $this->id);
    }

    /**
     * กำหนดให้ข้อมูลตารางเริ่มที่ Row 7 เพื่อเว้นที่ว่างให้หัวเอกสาร
     */
    public function startCell(): string {
        return 'A7';
    }

    public function headings(): array {
        return [
            ['ลำดับ', 'รายการสินค้า', 'จำนวน', 'หน่วยละ', 'ส่วนลด', 'จำนวนเงิน']
        ];
    }

    public function map($sale): array {
        $rows = [];
        $items = $sale->items;
        $this->itemCount = $items->count();
        $subTotal = 0;

        foreach ($items as $index => $item) {
            $lineTotal = $item->quantity * $item->unit_price;
            $subTotal += $lineTotal;
            $rows[] = [
                $index + 1,
                $item->description,
                $item->quantity,
                $item->unit_price,
                0, // ส่วนลด (ถ้ามี)
                $lineTotal,
            ];
        }

        $vat = $subTotal * 0.07;
        $total = $subTotal + $vat;

        $rows[] = ['', '', '', '', 'รวมเงิน:', $subTotal];
        $rows[] = ['', '', '', '', 'ภาษีมูลค่าเพิ่ม 7%:', $vat];
        $rows[] = ['', '', '', '', 'จำนวนเงินรวมทั้งสิ้น:', $total];

        return $rows;
    }

    public function columnFormats(): array {
        return [
            'C' => '#,##0',
            'D' => '#,##0.00',
            'F' => '#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet) {
        $sale = $this->saleData;

        // --- การจัดวางส่วนหัว (Header Section) ---
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'ใบกำกับภาษี / ใบแจ้งหนี้ (TAX INVOICE / INVOICE)');

        // ข้อมูลลูกค้า (ฝั่งซ้าย)
        $sheet->setCellValue('A3', 'ชื่อลูกค้า: ' . $sale->customer->name);
        $sheet->setCellValue('A4', 'ที่อยู่: ' . ($sale->customer->address ?? '-'));
        $sheet->setCellValue('A5', 'เลขประจำตัวผู้เสียภาษี: ' . ($sale->customer->tax_id ?? '-'));

        // ข้อมูลเลขที่เอกสาร (ฝั่งขวา)
        $sheet->setCellValue('E3', 'เลขที่:'); $sheet->setCellValue('F3', $sale->doc_no);
        $sheet->setCellValue('E4', 'วันที่:'); $sheet->setCellValue('F4', \Carbon\Carbon::parse($sale->doc_date)->format('d/m/Y'));
        $sheet->setCellValue('E5', 'สาขา:'); $sheet->setCellValue('F5', $sale->branch->name ?? 'สำนักงานใหญ่');

        // --- การตกแต่ง (Styling) ---
        $lastRow = 7 + $this->itemCount + 2; // + หัวตาราง + จำนวนสินค้า + 3 แถวสรุป

        // จัดกึ่งกลางหัวเรื่องใหญ่
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // หัวตารางรายการสินค้า (Row 7)
        $sheet->getStyle('A7:F7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A4A4A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ใส่เส้นขอบเฉพาะส่วนตารางรายการ
        $sheet->getStyle("A7:F$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // จัดชิดขวาตัวเลข
        $sheet->getStyle("C8:F$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
