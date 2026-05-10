<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class CustomersImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    // กำหนดการตั้งค่าการอ่านไฟล์
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',',
        ];
    }

    public function model(array $row)
    {
        // Debug: ตรวจสอบข้อมูลที่อ่านได้
        \Log::info('Import row data:', $row);

        return DB::transaction(function () use ($row) {
            $last = Customer::lockForUpdate()->orderBy('id', 'desc')->first();

            if ($last && $last->code) {
                $num = intval(substr($last->code, -5));
                $next = str_pad($num + 1, 5, '0', STR_PAD_LEFT);
                $code = 'CUS-' . $next;
            } else {
                $code = 'CUS-00001';
            }

            return new Customer([
                'code'    => $code,
                'name'    => $row['name'] ?? $row['ชื่อ'] ?? null,
                'phone'   => $row['phone'] ?? $row['โทรศัพท์'] ?? null,
                'address' => $row['address'] ?? $row['ที่อยู่'] ?? null,
                'email'   => $row['email'] ?? $row['อีเมล'] ?? null,
                'tax_id'  => $row['tax_id'] ?? $row['เลขประจำตัวผู้เสียภาษี'] ?? null,
            ]);
        });
    }
}
