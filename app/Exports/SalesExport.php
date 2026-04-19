<?php

namespace App\Exports;

use App\Models\Sale;
// สังเกตตัวพิมพ์ใหญ่ Q และ H ให้ดีครับ
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;
    protected $status;

    public function __construct($search, $status)
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function query()
    {
        $query = Sale::query();
        if ($this->search) $query->where('doc_no', 'like', '%' . $this->search . '%');
        if ($this->status) $query->where('status', $this->status);

        return $query;
    }

    public function headings(): array
    {
        return ["วันที่เอกสาร", "เลขที่เอกสาร", "ยอดเงินรวมสุทธิ", "สถานะ"];
    }

    public function map($sale): array
    {
        return [
            $sale->doc_date,
            $sale->doc_no,
            $sale->total,
            $sale->status
        ];
    }
}
