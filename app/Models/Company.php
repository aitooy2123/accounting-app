<?php
// app/Models/Company.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'email', 'phone', 'address', 'tax_id','is_active'
    ];

    // Method สำหรับสร้าง Auto Code
    public static function generateCode()
    {
        $prefix = 'CMP';
        $year = date('Y');
        $month = date('m');

        // ดึงลำดับล่าสุด
        $lastCompany = self::whereYear('created_at', $year)
                           ->orderBy('id', 'desc')
                           ->first();

        if ($lastCompany) {
            $lastCode = $lastCompany->code;
            $lastNumber = intval(substr($lastCode, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $year . $month . $newNumber;
        // ตัวอย่าง: CMP2024010001
    }
}
