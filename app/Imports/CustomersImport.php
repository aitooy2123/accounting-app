<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class CustomersImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    /**
     * ตั้งค่า CSV
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',',
        ];
    }

    /**
     * Import Model
     */
    public function model(array $row)
    {
        // Debug
        \Log::info('Import row data:', $row);

        return DB::transaction(function () use ($row) {

            $last = Customer::lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if ($last && $last->code) {

                $num = intval(substr($last->code, -5));

                $next = str_pad($num + 1, 5, '0', STR_PAD_LEFT);

                $code = 'CUS-' . $next;

            } else {

                $code = 'CUS-00001';
            }

            // ป้องกัน name เป็น null
            $name =
                !empty(trim($row['name'] ?? ''))
                ? trim($row['name'])
                : (
                    !empty(trim($row['ชื่อ'] ?? ''))
                    ? trim($row['ชื่อ'])
                    : 'ลูกค้าทั่วไป'
                );

            return new Customer([

                'code' => $code,

                'name' => $name,

                'phone' =>
                    $row['phone']
                    ?? $row['โทรศัพท์']
                    ?? '',

                'address' =>
                    $row['address']
                    ?? $row['ที่อยู่']
                    ?? '',

                'email' =>
                    $row['email']
                    ?? $row['อีเมล']
                    ?? '',

                'tax_id' =>
                    $row['tax_id']
                    ?? $row['เลขประจำตัวผู้เสียภาษี']
                    ?? '',
            ]);
        });
    }
}
