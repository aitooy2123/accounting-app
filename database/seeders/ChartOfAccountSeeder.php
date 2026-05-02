<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // หากต้องการเก็บข้อมูลสินทรัพย์ที่เคยลงไว้ ให้เอา ChartOfAccount::truncate() ออก
        // แต่ถ้าต้องการล้างเพื่อลงใหม่ทั้งหมดให้คงไว้ครับ
        ChartOfAccount::truncate();

        $data = [
            // --- 2000 หนี้สิน ---
            ['code' => '2000-00', 'name_th' => 'หนี้สิน', 'category' => 'liability', 'is_group' => true, 'p_code' => null],
            ['code' => '2100-00', 'name_th' => 'หนี้สินหมุนเวียน', 'category' => 'liability', 'is_group' => true, 'p_code' => '2000-00'],
            ['code' => '2120-00', 'name_th' => 'เจ้าหนี้การค้าและตั๋วเงินจ่าย', 'category' => 'liability', 'is_group' => true, 'p_code' => '2100-00'],
            ['code' => '2120-01', 'name_th' => 'เจ้าหนี้การค้า', 'category' => 'liability', 'is_group' => false, 'p_code' => '2120-00'],
            ['code' => '2120-02', 'name_th' => 'เช็คจ่ายล่วงหน้า', 'category' => 'liability', 'is_group' => false, 'p_code' => '2120-00'],
            ['code' => '2130-00', 'name_th' => 'หนี้สินหมุนเวียนอื่น', 'category' => 'liability', 'is_group' => true, 'p_code' => '2100-00'],
            ['code' => '2131-00', 'name_th' => 'ค่าใช้จ่ายค้างจ่าย', 'category' => 'liability', 'is_group' => true, 'p_code' => '2130-00'],
            ['code' => '2131-01', 'name_th' => 'เงินเดือนค้างจ่าย', 'category' => 'liability', 'is_group' => false, 'p_code' => '2131-00'],
            ['code' => '2131-06', 'name_th' => 'ค่าไฟฟ้าค้างจ่าย', 'category' => 'liability', 'is_group' => false, 'p_code' => '2131-00'],
            ['code' => '2135-00', 'name_th' => 'ภาษีขาย', 'category' => 'liability', 'is_group' => false, 'p_code' => '2130-00'],
            ['code' => '2200-00', 'name_th' => 'เงินกู้ยืมระยะยาว', 'category' => 'liability', 'is_group' => true, 'p_code' => '2000-00'],

            // --- 3000 ส่วนของผู้ถือหุ้น ---
            ['code' => '3000-00', 'name_th' => 'ส่วนของผู้ถือหุ้น', 'category' => 'equity', 'is_group' => true, 'p_code' => null],
            ['code' => '3100-00', 'name_th' => 'ทุน', 'category' => 'equity', 'is_group' => false, 'p_code' => '3000-00'],
            ['code' => '3200-00', 'name_th' => 'กำไรสะสม', 'category' => 'equity', 'is_group' => false, 'p_code' => '3000-00'],

            // --- 4000 รายได้ ---
            ['code' => '4000-00', 'name_th' => 'รายได้', 'category' => 'revenue', 'is_group' => true, 'p_code' => null],
            ['code' => '4100-00', 'name_th' => 'รายได้จากการขายสินค้า-สุทธิ', 'category' => 'revenue', 'is_group' => true, 'p_code' => '4000-00'],
            ['code' => '4100-01', 'name_th' => 'รายได้จากการขาย', 'category' => 'revenue', 'is_group' => false, 'p_code' => '4100-00'],
            ['code' => '4100-02', 'name_th' => 'รายได้จากการให้บริการ', 'category' => 'revenue', 'is_group' => false, 'p_code' => '4100-00'],
            ['code' => '4200-00', 'name_th' => 'รายได้อื่น ๆ', 'category' => 'revenue', 'is_group' => true, 'p_code' => '4000-00'],
            ['code' => '4200-01', 'name_th' => 'ดอกเบี้ยรับ', 'category' => 'revenue', 'is_group' => false, 'p_code' => '4200-00'],

            // --- 5000 ค่าใช้จ่าย ---
            ['code' => '5000-00', 'name_th' => 'ค่าใช้จ่าย', 'category' => 'expense', 'is_group' => true, 'p_code' => null],
            ['code' => '5100-00', 'name_th' => 'ต้นทุนขายสุทธิ', 'category' => 'expense', 'is_group' => true, 'p_code' => '5000-00'],
            ['code' => '5130-00', 'name_th' => 'ซื้อสุทธิ', 'category' => 'expense', 'is_group' => true, 'p_code' => '5100-00'],
            ['code' => '5130-01', 'name_th' => 'ซื้อ', 'category' => 'expense', 'is_group' => false, 'p_code' => '5130-00'],
            ['code' => '5200-00', 'name_th' => 'ค่าใช้จ่ายในการขาย', 'category' => 'expense', 'is_group' => true, 'p_code' => '5000-00'],
            ['code' => '5200-05', 'name_th' => 'ค่าขนส่ง', 'category' => 'expense', 'is_group' => false, 'p_code' => '5200-00'],
            ['code' => '5300-00', 'name_th' => 'ค่าใช้จ่ายในการบริหาร', 'category' => 'expense', 'is_group' => true, 'p_code' => '5000-00'],
            ['code' => '5310-00', 'name_th' => 'ค่าใช้จ่ายเกี่ยวกับพนักงาน', 'category' => 'expense', 'is_group' => true, 'p_code' => '5300-00'],
            ['code' => '5310-01', 'name_th' => 'เงินเดือน', 'category' => 'expense', 'is_group' => false, 'p_code' => '5310-00'],
            ['code' => '5330-00', 'name_th' => 'ค่าสาธารณูปโภคและสื่อสาร', 'category' => 'expense', 'is_group' => true, 'p_code' => '5300-00'],
            ['code' => '5330-02', 'name_th' => 'ค่าไฟฟ้า', 'category' => 'expense', 'is_group' => false, 'p_code' => '5330-00'],
        ];

        foreach ($data as $item) {
            $parentId = null;
            if ($item['p_code']) {
                $parent = ChartOfAccount::where('code', $item['p_code'])->first();
                $parentId = $parent ? $parent->id : null;
            }

            ChartOfAccount::create([
                'code' => $item['code'],
                'name_th' => $item['name_th'],
                'category' => $item['category'],
                'is_group' => $item['is_group'],
                'parent_id' => $parentId,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
