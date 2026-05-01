<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomersImport implements ToModel
{
    public function model(array $row)
    {
        return new Customer([
            'code' => $row[0],
            'name' => $row[1],
            'phone' => $row[2],
            'email' => $row[3],
        ]);
    }
}
