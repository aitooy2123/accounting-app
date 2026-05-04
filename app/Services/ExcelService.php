<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelService
{
    public function createCustomerTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ header (ตรงกับ import)
        $headers = [
            'A1' => 'code',
            'B1' => 'name',
            'C1' => 'address',
            'D1' => 'email',
            'E1' => 'phone',
            'F1' => 'tax_id',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // ✅ example ถูกคอลัมน์
        $sheet->setCellValue('B2', 'บริษัท ABC จำกัด');
        $sheet->setCellValue('C2', 'กรุงเทพมหานคร');
        $sheet->setCellValue('D2', 'example@company.com');
        $sheet->setCellValue('E2', '0812345678');
        $sheet->setCellValue('F2', '1234567890123');

        return $spreadsheet;
    }

    public function generateCustomerCode()
    {
        $last = \App\Models\Customer::orderBy('id', 'desc')->first();

        $number = $last
            ? (int) substr($last->code, -5) + 1
            : 1;

        return 'CUS-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * ✅ map แบบถูกต้องตาม index
     */
    public function mapCustomerData(array $row)
    {
        return [
            'code' => $this->generateCustomerCode(),
            'name' => trim($row[1] ?? ''),     // B
            'address' => trim($row[2] ?? ''),  // C
            'email' => trim($row[3] ?? ''),    // D
            'phone' => preg_replace('/[^0-9]/', '', $row[4] ?? ''), // E
            'tax_id' => trim($row[5] ?? ''),   // F
        ];
    }

    public function validateRow($data)
    {
        return \Validator::make($data, [
            'name' => 'required',
            'phone' => 'required|digits_between:9,10',
            'email' => 'nullable|email',
            'tax_id' => 'nullable|digits:13',
        ]);
    }
}
