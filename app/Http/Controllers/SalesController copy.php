<?php

namespace App\Http\Controllers;

use App\Models\Sale; // ตรวจสอบชื่อ Model ของคุณ (อาจเป็น Sale หรือ Sales)
use Illuminate\Http\Request;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
{
    /**
     * แสดงรายการขายทั้งหมด พร้อมระบบค้นหาและแบ่งหน้า
     */
    public function index(Request $request)
    {
        $query = Sale::query();

        // ค้นหาจากเลขที่เอกสาร
        if ($request->filled('search')) {
            $query->where('doc_no', 'like', '%' . $request->search . '%');
        }

        // กรองจากสถานะ
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ดึงข้อมูลพร้อมทำ Pagination
        $sales = $query->orderBy('doc_date', 'desc')->paginate(10);

        return view('pages.sale.index', compact('sales'));
    }

    /**
     * ส่งออกข้อมูลเป็นไฟล์ Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $fileName = 'sales_report_' . date('Ymd_His') . '.xlsx';

        // ส่งค่า search และ status ไปยัง Export Class เพื่อให้ได้ข้อมูลตามที่กรองบนหน้าเว็บ
        return Excel::download(
            new SalesExport($request->search, $request->status),
            $fileName
        );
    }

    /**
     * สร้างไฟล์ PDF ใบแจ้งหนี้
     */
    public function generatePdf($id)
    {
        $sale = Sale::findOrFail($id);

        $data = [
            'sale' => $sale,
            'title' => 'ใบแจ้งหนี้ ' . $sale->doc_no
        ];

        // โหลด view สำหรับทำ PDF (ต้องสร้างไฟล์ resources/views/pages/sales_pdf_view.blade.php)
        $pdf = Pdf::loadView('pages.sales_pdf_view', $data)
                  ->setPaper('a4')
                  ->setOption(['defaultFont' => 'THSarabunNew']);

        return $pdf->stream($sale->doc_no . '.pdf');
    }

    /**
     * ลบเอกสาร
     */
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        return redirect()->route('pages.sales')->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }

    // เพิ่ม Method create, store, edit, update ตามความเหมาะสมของโปรเจกต์
}
