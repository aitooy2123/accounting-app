<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
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
                'code'   => $code,
                'name'   => $row['name'] ?? null,
                'phone'  => $row['phone'] ?? null,
                'address' => $row['address'] ?? null,
                'email'  => $row['email'] ?? null,
                'tax_id' => $row['tax_id'] ?? null,
            ]);
        });
    }
}
