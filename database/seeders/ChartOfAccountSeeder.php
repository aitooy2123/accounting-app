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
    // ==================== 1000 สินทรัพย์ ====================
    // --- 1100 สินทรัพย์หมุนเวียน ---
    ['code' => '1000-00', 'name_th' => 'สินทรัพย์', 'category' => 'asset', 'is_group' => true, 'p_code' => null],
    ['code' => '1100-00', 'name_th' => 'สินทรัพย์หมุนเวียน', 'category' => 'asset', 'is_group' => true, 'p_code' => '1000-00'],
    ['code' => '1110-00', 'name_th' => 'เงินสดและเงินฝากธนาคาร', 'category' => 'asset', 'is_group' => true, 'p_code' => '1100-00'],
    ['code' => '1111-00', 'name_th' => 'เงินสด', 'category' => 'asset', 'is_group' => false, 'p_code' => '1110-00'],
    ['code' => '1111-50', 'name_th' => 'เงินสดย่อย', 'category' => 'asset', 'is_group' => false, 'p_code' => '1110-00'],
    ['code' => '1112-00', 'name_th' => 'เงินฝากกระแสรายวัน', 'category' => 'asset', 'is_group' => true, 'p_code' => '1110-00'],
    ['code' => '1112-01', 'name_th' => 'เงินฝากกระแสรายวัน 999-9-99999', 'category' => 'asset', 'is_group' => false, 'p_code' => '1112-00'],
    ['code' => '1112-02', 'name_th' => 'เงินฝากกระแสรายวัน 999-9-99999', 'category' => 'asset', 'is_group' => false, 'p_code' => '1112-00'],
    ['code' => '1112-03', 'name_th' => 'เงินฝากกระแสรายวัน 123-456-789', 'category' => 'asset', 'is_group' => false, 'p_code' => '1112-00'],
    ['code' => '1113-00', 'name_th' => 'เงินฝากออมทรัพย์', 'category' => 'asset', 'is_group' => true, 'p_code' => '1110-00'],
    ['code' => '1113-01', 'name_th' => 'เงินฝากออมทรัพย์ 999-9-99999-1', 'category' => 'asset', 'is_group' => false, 'p_code' => '1113-00'],
    ['code' => '1113-02', 'name_th' => 'เงินฝากออมทรัพย์ 999-9-99999-2', 'category' => 'asset', 'is_group' => false, 'p_code' => '1113-00'],
    ['code' => '1113-03', 'name_th' => 'เงินฝากออมทรัพย์ 999-9-99999-3', 'category' => 'asset', 'is_group' => false, 'p_code' => '1113-00'],
    ['code' => '1114-00', 'name_th' => 'เงินฝากประจำ', 'category' => 'asset', 'is_group' => true, 'p_code' => '1110-00'],
    ['code' => '1114-01', 'name_th' => 'เงินฝากประจำ 999-9-99999-1', 'category' => 'asset', 'is_group' => false, 'p_code' => '1114-00'],
    ['code' => '1114-02', 'name_th' => 'เงินฝากประจำ 999-9-99999-2', 'category' => 'asset', 'is_group' => false, 'p_code' => '1114-00'],
    ['code' => '1114-03', 'name_th' => 'เงินฝากประจำ 999-9-99999-3', 'category' => 'asset', 'is_group' => false, 'p_code' => '1114-00'],
    ['code' => '1120-00', 'name_th' => 'เงินลงทุนระยะสั้น', 'category' => 'asset', 'is_group' => false, 'p_code' => '1100-00'],
    ['code' => '1130-00', 'name_th' => 'ลูกหนี้การค้าและตั๋วเงินรับ', 'category' => 'asset', 'is_group' => true, 'p_code' => '1100-00'],
    ['code' => '1130-01', 'name_th' => 'ลูกหนี้การค้า', 'category' => 'asset', 'is_group' => false, 'p_code' => '1130-00'],
    ['code' => '1130-02', 'name_th' => 'เช็ครับลงวันที่ล่วงหน้า', 'category' => 'asset', 'is_group' => false, 'p_code' => '1130-00'],
    ['code' => '1130-03', 'name_th' => 'ค่าเผื่อหนี้สงสัยจะสูญ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1130-00'], // contra asset
    ['code' => '1130-04', 'name_th' => 'ลูกหนี้อื่น ๆ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1130-00'],
    ['code' => '1140-00', 'name_th' => 'สินค้าคงเหลือ', 'category' => 'asset', 'is_group' => true, 'p_code' => '1100-00'],
    ['code' => '1140-01', 'name_th' => 'วัตถุดิบคงเหลือ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1140-00'],
    ['code' => '1140-02', 'name_th' => 'สินค้าสำเร็จรูปคงเหลือ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1140-00'],
    ['code' => '1140-03', 'name_th' => 'งานระหว่างทำ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1140-00'],
    ['code' => '1150-00', 'name_th' => 'สินทรัพย์หมุนเวียนอื่น ๆ', 'category' => 'asset', 'is_group' => true, 'p_code' => '1100-00'],
    ['code' => '1151-00', 'name_th' => 'ค่าใช้จ่ายจ่ายล่วงหน้า', 'category' => 'asset', 'is_group' => true, 'p_code' => '1150-00'],
    ['code' => '1151-01', 'name_th' => 'ค่าใช้จ่ายจ่ายล่วงหน้า-ค่าสินค้า', 'category' => 'asset', 'is_group' => false, 'p_code' => '1151-00'],
    ['code' => '1151-02', 'name_th' => 'ภาษีนิติบุคคลจ่ายล่วงหน้า', 'category' => 'asset', 'is_group' => false, 'p_code' => '1151-00'],
    ['code' => '1151-03', 'name_th' => 'ค่าใช้จ่ายจ่ายล่วงหน้า-ค่าเช่า', 'category' => 'asset', 'is_group' => false, 'p_code' => '1151-00'],
    ['code' => '1151-04', 'name_th' => 'ค่าใช้จ่ายจ่ายล่วงหน้า-อื่น ๆ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1151-00'],
    ['code' => '1152-00', 'name_th' => 'เงินทดลองจ่ายพนักงาน', 'category' => 'asset', 'is_group' => false, 'p_code' => '1150-00'],
    ['code' => '1153-00', 'name_th' => 'รายได้ค้างรับ', 'category' => 'asset', 'is_group' => true, 'p_code' => '1150-00'],
    ['code' => '1153-01', 'name_th' => 'ดอกเบี้ยค้างรับ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1153-00'],
    ['code' => '1153-02', 'name_th' => 'รายได้ค้างรับอื่น', 'category' => 'asset', 'is_group' => false, 'p_code' => '1153-00'],
    ['code' => '1154-00', 'name_th' => 'ภาษีซื้อ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1150-00'],
    ['code' => '1155-00', 'name_th' => 'ภาษีซื้อ-ยังไม่ถึงกำหนด', 'category' => 'asset', 'is_group' => false, 'p_code' => '1150-00'],
    ['code' => '1156-00', 'name_th' => 'ลูกหนี้-กรมสรรพากร', 'category' => 'asset', 'is_group' => false, 'p_code' => '1150-00'],
    ['code' => '1200-00', 'name_th' => 'ลูกหนี้เงินให้กู้ยืมแก่กรรมการและลูกจ้าง', 'category' => 'asset', 'is_group' => false, 'p_code' => '1100-00'],
    ['code' => '1210-00', 'name_th' => 'ลูกหนี้เงินให้กู้ยืม-นาย...', 'category' => 'asset', 'is_group' => false, 'p_code' => '1100-00'],

    // --- 1300 เงินลงทุนในบริษัทในเครือ (ไม่หมุนเวียน) ---
    ['code' => '1300-00', 'name_th' => 'เงินลงทุนในบริษัทในเครือ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1000-00'],

    // --- 1400 ที่ดิน อาคารและอุปกรณ์ (สินทรัพย์ไม่หมุนเวียน) ---
    ['code' => '1400-00', 'name_th' => 'ที่ดิน อาคารและอุปกรณ์สุทธิ', 'category' => 'asset', 'is_group' => true, 'p_code' => '1000-00'],
    ['code' => '1410-00', 'name_th' => 'ที่ดิน อาคาร และอุปกรณ์', 'category' => 'asset', 'is_group' => true, 'p_code' => '1400-00'],
    ['code' => '1410-01', 'name_th' => 'ที่ดิน', 'category' => 'asset', 'is_group' => false, 'p_code' => '1410-00'],
    ['code' => '1410-02', 'name_th' => 'อาคาร', 'category' => 'asset', 'is_group' => false, 'p_code' => '1410-00'],
    ['code' => '1410-03', 'name_th' => 'อุปกรณ์สำนักงาน', 'category' => 'asset', 'is_group' => false, 'p_code' => '1410-00'],
    ['code' => '1410-04', 'name_th' => 'เครื่องตกแต่งสำนักงาน', 'category' => 'asset', 'is_group' => false, 'p_code' => '1410-00'],
    ['code' => '1410-05', 'name_th' => 'ยานพาหนะ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1410-00'],
    ['code' => '1420-00', 'name_th' => 'ค่าเสื่อมราคาสะสม', 'category' => 'asset', 'is_group' => true, 'p_code' => '1400-00'], // contra asset
    ['code' => '1420-02', 'name_th' => 'ค่าเสื่อมราคาสะสม-อาคาร', 'category' => 'asset', 'is_group' => false, 'p_code' => '1420-00'],
    ['code' => '1420-03', 'name_th' => 'ค่าเสื่อมราคาสะสม-อุปกรณ์สำนักงาน', 'category' => 'asset', 'is_group' => false, 'p_code' => '1420-00'],
    ['code' => '1420-04', 'name_th' => 'ค่าเสื่อมราคาสะสม-เครื่องตกแต่งสำนักงาน', 'category' => 'asset', 'is_group' => false, 'p_code' => '1420-00'],
    ['code' => '1420-05', 'name_th' => 'ค่าเสื่อมราคาสะสม-ยานพาหนะ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1420-00'],

    // --- 1500 สินทรัพย์อื่น ๆ ---
    ['code' => '1500-00', 'name_th' => 'สินทรัพย์อื่น ๆ', 'category' => 'asset', 'is_group' => true, 'p_code' => '1000-00'],
    ['code' => '1500-01', 'name_th' => 'กรมธรรม์ประกันอัคคีภัย-สินค้าและอาคาร', 'category' => 'asset', 'is_group' => false, 'p_code' => '1500-00'],
    ['code' => '1500-02', 'name_th' => 'กรมธรรม์ประกันอัคคีภัย-ยานพาหนะ', 'category' => 'asset', 'is_group' => false, 'p_code' => '1500-00'],
    ['code' => '1500-03', 'name_th' => 'กรมธรรม์ประกันอุบัติเหตุพนักงาน', 'category' => 'asset', 'is_group' => false, 'p_code' => '1500-00'],
    ['code' => '1500-04', 'name_th' => 'พันธบัตรโทรศัพท์', 'category' => 'asset', 'is_group' => false, 'p_code' => '1500-00'],

    // ==================== 2000 หนี้สิน ====================
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

    // ==================== 3000 ส่วนของผู้ถือหุ้น ====================
    ['code' => '3000-00', 'name_th' => 'ส่วนของผู้ถือหุ้น', 'category' => 'equity', 'is_group' => true, 'p_code' => null],
    ['code' => '3100-00', 'name_th' => 'ทุน', 'category' => 'equity', 'is_group' => false, 'p_code' => '3000-00'],
    ['code' => '3200-00', 'name_th' => 'กำไรสะสม', 'category' => 'equity', 'is_group' => false, 'p_code' => '3000-00'],

    // ==================== 4000 รายได้ ====================
    ['code' => '4000-00', 'name_th' => 'รายได้', 'category' => 'revenue', 'is_group' => true, 'p_code' => null],
    ['code' => '4100-00', 'name_th' => 'รายได้จากการขายสินค้า-สุทธิ', 'category' => 'revenue', 'is_group' => true, 'p_code' => '4000-00'],
    ['code' => '4100-01', 'name_th' => 'รายได้จากการขาย', 'category' => 'revenue', 'is_group' => false, 'p_code' => '4100-00'],
    ['code' => '4100-02', 'name_th' => 'รายได้จากการให้บริการ', 'category' => 'revenue', 'is_group' => false, 'p_code' => '4100-00'],
    ['code' => '4200-00', 'name_th' => 'รายได้อื่น ๆ', 'category' => 'revenue', 'is_group' => true, 'p_code' => '4000-00'],
    ['code' => '4200-01', 'name_th' => 'ดอกเบี้ยรับ', 'category' => 'revenue', 'is_group' => false, 'p_code' => '4200-00'],

    // ==================== 5000 ค่าใช้จ่าย ====================
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
