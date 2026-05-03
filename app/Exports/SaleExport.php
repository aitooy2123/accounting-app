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
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SaleExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithProperties
{
    protected $id;
    protected $itemCount = 0;
    protected $saleData;

    public function __construct($id) {
        $this->id = $id;
        $this->saleData = Sale::with(['customer', 'branch'])->find($id);
    }

    public function properties(): array {
        return [
            'creator' => 'Your Company Name',
            'lastModifiedBy' => 'Your Company Name',
            'title' => 'Invoice',
            'description' => 'Sales Invoice',
            'subject' => 'Invoice',
            'keywords' => 'invoice,sales,tax',
            'category' => 'Invoice',
            'manager' => 'Manager',
            'company' => 'Your Company Name',
        ];
    }

    public function query() {
        return Sale::with(['items'])->where('id', $this->id);
    }

    public function startCell(): string {
        return 'A7';
    }

    public function headings(): array {
        return [
            'ลำดับ', 'รายการสินค้า', 'จำนวน', 'หน่วยละ', 'ส่วนลด', 'จำนวนเงิน'
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
                0,
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

        if (!$sale) {
            return [];
        }

        // --- Page Setup for A4 Portrait ---
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true)
            ->setVerticalCentered(false);

        // Set margins (in inches)
        $sheet->getPageMargins()
            ->setTop(0.75)
            ->setBottom(0.75)
            ->setLeft(0.7)
            ->setRight(0.7)
            ->setHeader(0.3)
            ->setFooter(0.3);

        // Set print area to fit content
        $dataEndRow = 7 + $this->itemCount + 3 + 5; // +5 for footer
        $sheet->getPageSetup()->setPrintArea("A1:F{$dataEndRow}");

        // Set header and footer
        $sheet->getHeaderFooter()->setOddHeader('&C&B&16ใบเสร็จรับเงิน / ใบกำกับภาษี');
        $sheet->getHeaderFooter()->setOddFooter('&Rหน้า &P จาก &N');

        // Scale to fit
        $sheet->getPageSetup()->setScale(85); // Slightly reduce to ensure everything fits

        // --- Header Section ---
        // Row 1: Main Title
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'ใบเสร็จรับเงิน / ใบกำกับภาษี');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Add spacing row
        $sheet->getRowDimension(2)->setRowHeight(5);

        // Row 3-5: Customer Info (Left) and Document Info (Right)
        $sheet->setCellValue('A3', 'ชื่อลูกค้า: ' . ($sale->customer->name ?? '-'));
        $sheet->setCellValue('A4', 'ที่อยู่: ' . ($sale->customer->address ?? '-'));
        $sheet->setCellValue('A5', 'เลขประจำตัวผู้เสียภาษี: ' . ($sale->customer->tax_id ?? '-'));

        $sheet->setCellValue('D3', 'เลขที่เอกสาร: ' . ($sale->invoice_no ?? '-'));
        $sheet->setCellValue('D4', 'วันที่: ' . ($sale->created_at ? $sale->created_at->format('d/m/Y') : '-'));

        if ($sale->branch) {
            $sheet->setCellValue('D5', 'สาขา: ' . ($sale->branch->name ?? '-'));
        }

        // Style info text
        $sheet->getStyle('A3:A5')->getFont()->setSize(10);
        $sheet->getStyle('D3:D5')->getFont()->setSize(10);
        $sheet->getStyle('A3:A5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D3:D5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(18);
        $sheet->getRowDimension(5)->setRowHeight(18);

        // Add spacing before table
        $sheet->getRowDimension(6)->setRowHeight(10);

        // --- Table Styling ---
        $dataStartRow = 7;
        $dataEndRow = $dataStartRow + $this->itemCount + 3; // +3 for summary rows

        // Table Header (Row 7)
        $sheet->getStyle("A{$dataStartRow}:F{$dataStartRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ]);

        // Set row height for header
        $sheet->getRowDimension($dataStartRow)->setRowHeight(22);

        // Apply borders to the entire table range
        $borderRange = "A{$dataStartRow}:F{$dataEndRow}";
        $sheet->getStyle($borderRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '333333']
                ]
            ]
        ]);

        // Style for data rows
        $dataRowsRange = "A" . ($dataStartRow + 1) . ":F" . ($dataEndRow - 3);
        if ($this->itemCount > 0) {
            $sheet->getStyle($dataRowsRange)->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'font' => ['size' => 10]
            ]);

            // Zebra striping for better readability
            for ($i = 0; $i < $this->itemCount; $i++) {
                $rowNum = $dataStartRow + 1 + $i;
                if ($i % 2 == 1) {
                    $sheet->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F9F9F9']
                        ]
                    ]);
                }
                $sheet->getRowDimension($rowNum)->setRowHeight(18);
            }
        }

        // Align numbers right for columns C, D, F
        $numberColumns = ['C', 'D', 'F'];
        foreach ($numberColumns as $column) {
            $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$dataEndRow}")
                  ->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Center align and adjust column A (ลำดับ)
        $sheet->getStyle("A{$dataStartRow}:A{$dataEndRow}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set column A width to optimal size (adjusted for better spacing)
        // Dynamic width based on number of items
        $maxNumber = $this->itemCount;
        if ($maxNumber < 10) {
            $columnAWidth = 5;  // Width for 1-9 items
        } elseif ($maxNumber < 100) {
            $columnAWidth = 6;  // Width for 10-99 items
        } elseif ($maxNumber < 1000) {
            $columnAWidth = 7;  // Width for 100-999 items
        } else {
            $columnAWidth = 8;  // Width for 1000+ items
        }
        $sheet->getColumnDimension('A')->setWidth($columnAWidth);

        // Style for summary rows
        $summaryStartRow = $dataStartRow + $this->itemCount + 1;

        // Add top border for summary section
        if ($this->itemCount > 0) {
            $sheet->getStyle("E{$summaryStartRow}:F{$summaryStartRow}")
                  ->getBorders()
                  ->getTop()
                  ->setBorderStyle(Border::BORDER_THIN);
        }

        // Bold and style summary rows
        for ($i = 0; $i < 3; $i++) {
            $currentRow = $summaryStartRow + $i;
            $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getFont()->setSize(10);
            $sheet->getRowDimension($currentRow)->setRowHeight(18);

            // Add background color for total row
            if ($i == 2) {
                $sheet->getStyle("E{$currentRow}:F{$currentRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE5CC']
                    ]
                ]);
            }
        }

        // Align text right for column E in summary rows
        $sheet->getStyle("E{$summaryStartRow}:E" . ($summaryStartRow + 2))
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Format currency for summary values (column F)
        $sheet->getStyle("F{$summaryStartRow}:F" . ($summaryStartRow + 2))
              ->getNumberFormat()
              ->setFormatCode('#,##0.00');

        // Set column widths for A4 fit (adjusted to fit within A4 width)
        $sheet->getColumnDimension('B')->setWidth(45);  // รายการสินค้า
        $sheet->getColumnDimension('C')->setWidth(10);  // จำนวน
        $sheet->getColumnDimension('D')->setWidth(13);  // หน่วยละ
        $sheet->getColumnDimension('E')->setWidth(13);  // ส่วนลด
        $sheet->getColumnDimension('F')->setWidth(16);  // จำนวนเงิน

        // Auto-wrap text for description column
        $sheet->getStyle("B" . ($dataStartRow + 1) . ":B" . ($dataEndRow - 3))
              ->getAlignment()
              ->setWrapText(true);

        // --- Footer Section ---
        $footerStartRow = $dataEndRow + 2;

        // Add company signature lines
        $sheet->setCellValue("A{$footerStartRow}", "ผู้รับเงิน_________________________");
        $sheet->setCellValue("D{$footerStartRow}", "ผู้จ่ายเงิน_________________________");
        $sheet->setCellValue("A" . ($footerStartRow + 1), "(__________________________)");
        $sheet->setCellValue("D" . ($footerStartRow + 1), "(__________________________)");
        $sheet->setCellValue("A" . ($footerStartRow + 2), "วันที่_________________________");
        $sheet->setCellValue("D" . ($footerStartRow + 2), "วันที่_________________________");

        $sheet->getStyle("A{$footerStartRow}:F" . ($footerStartRow + 2))
              ->getFont()
              ->setSize(9);

        $sheet->getStyle("A{$footerStartRow}:F" . ($footerStartRow + 2))
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A{$footerStartRow}:F" . ($footerStartRow + 2))
              ->getAlignment()
              ->setVertical(Alignment::VERTICAL_CENTER);

        for ($i = 0; $i < 3; $i++) {
            $sheet->getRowDimension($footerStartRow + $i)->setRowHeight(20);
        }

        // Freeze panes to keep header visible when scrolling
        $sheet->freezePane("A" . ($dataStartRow + 1));

        // Repeat header rows on each page
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($dataStartRow, $dataStartRow);

        return [];
    }
}
