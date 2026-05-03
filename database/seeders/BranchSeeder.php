<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ดึง company ทั้งหมด
        $companies = Company::all();

        if ($companies->isEmpty()) {
            // ถ้าไม่มีบริษัท ให้ใช้ company_id = 1 เป็นค่าเริ่มต้น
            $this->command->warn('⚠️ ไม่พบข้อมูลบริษัท จะใช้ company_id = 1 เป็นค่าเริ่มต้น');
        }

        $branches = [
            // ==================== บริษัทที่ 1 ====================
            [
                'code' => 'BR-001',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-123-4567',
                'email' => 'headquarter@company1.co.th',
                'address' => '123 ถนนสีลม แขวงสีลม เขตบางรัก กรุงเทพมหานคร 10500',
                'manager' => 'นายสมชาย ใจดี',
                'company_id' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'BR-002',
                'name' => 'สาขาลาดพร้าว',
                'phone' => '02-123-4568',
                'email' => 'ladprao@company1.co.th',
                'address' => '456 ถนนลาดพร้าว แขวงจอมพล เขตจตุจักร กรุงเทพมหานคร 10900',
                'manager' => 'นางสาวสมศรี รักดี',
                'company_id' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'BR-003',
                'name' => 'สาขาบางนา',
                'phone' => '02-123-4569',
                'email' => 'bangna@company1.co.th',
                'address' => '789 ถนนบางนา-ตราด แขวงบางนา เขตบางนา กรุงเทพมหานคร 10260',
                'manager' => 'นายวิชัย มั่นคง',
                'company_id' => 1,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 2 ====================
            [
                'code' => 'BR-004',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-987-6543',
                'email' => 'info@company2.co.th',
                'address' => '456 ถนนพระราม 9 แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพมหานคร 10310',
                'manager' => 'นายประเสริฐ สุขสันต์',
                'company_id' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'BR-005',
                'name' => 'สาขาอยุธยา',
                'phone' => '035-987-654',
                'email' => 'ayutthaya@company2.co.th',
                'address' => '123 ถนนโรจนะ ตำบลไผ่ลิง อำเภอพระนครศรีอยุธยา พระนครศรีอยุธยา 13000',
                'manager' => 'นางสาวอรุณี แสงทอง',
                'company_id' => 2,
                'is_active' => false,
            ],

            // ==================== บริษัทที่ 3 ====================
            [
                'code' => 'BR-006',
                'name' => 'สำนักงานใหญ่',
                'phone' => '038-456-789',
                'email' => 'info@company3.co.th',
                'address' => '789 ถนนสุขุมวิท ตำบลศรีราชา อำเภอศรีราชา ชลบุรี 20110',
                'manager' => 'นายสุรพล วัฒนา',
                'company_id' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'BR-007',
                'name' => 'สาขาพัทยา',
                'phone' => '038-456-780',
                'email' => 'pattaya@company3.co.th',
                'address' => '456 ถนนพัทยาใต้ ตำบลหนองปรือ อำเภอบางละมุง ชลบุรี 20150',
                'manager' => 'นายสันติ ใจเย็น',
                'company_id' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'BR-008',
                'name' => 'สาขาระยอง',
                'phone' => '038-456-781',
                'email' => 'rayong@company3.co.th',
                'address' => '789 ถนนสุขุมวิท ตำบลเชิงเนิน อำเภอเมืองระยอง ระยอง 21000',
                'manager' => 'นายตะวันออก ทะเลสวย',
                'company_id' => 3,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 4 ====================
            [
                'code' => 'BR-009',
                'name' => 'สำนักงานใหญ่',
                'phone' => '053-123-456',
                'email' => 'info@company4.co.th',
                'address' => '101 ถนนนิมมานเหมินทร์ ตำบลสุเทพ อำเภอเมืองเชียงใหม่ เชียงใหม่ 50200',
                'manager' => 'นางสาวพิมพ์ใจ ศิลปิน',
                'company_id' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'BR-010',
                'name' => 'สาขาลำพูน',
                'phone' => '053-123-457',
                'email' => 'lamphun@company4.co.th',
                'address' => '222 ถนนลำพูน-ป่าซาง ตำบลในเมือง อำเภอเมืองลำพูน ลำพูน 51000',
                'manager' => 'นายเหนือ ดอยสูง',
                'company_id' => 4,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 5 ====================
            [
                'code' => 'BR-011',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-345-6789',
                'email' => 'info@company5.co.th',
                'address' => '234 ถนนบางนา-ตราด ตำบลบางแก้ว อำเภอบางพลี สมุทรปราการ 10540',
                'manager' => 'นายสมบัติ ร่ำรวย',
                'company_id' => 5,
                'is_active' => false,
            ],
            [
                'code' => 'BR-012',
                'name' => 'โรงงานผลิต สมุทรสาคร',
                'phone' => '034-345-678',
                'email' => 'factory@company5.co.th',
                'address' => '567 ถนนเศรษฐกิจ ตำบลมหาชัย อำเภอเมืองสมุทรสาคร สมุทรสาคร 74000',
                'manager' => 'นายประสิทธิ์ ขยันดี',
                'company_id' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'BR-013',
                'name' => 'คลังสินค้า อยุธยา',
                'phone' => '035-345-678',
                'email' => 'warehouse@company5.co.th',
                'address' => '890 ถนนสายเอเชีย ตำบลคลองสวนพลู อำเภอพระนครศรีอยุธยา พระนครศรีอยุธยา 13000',
                'manager' => 'นายคลัง สินค้า',
                'company_id' => 5,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 6 ====================
            [
                'code' => 'BR-014',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-567-8901',
                'email' => 'info@company6.co.th',
                'address' => '567 ถนนแจ้งวัฒนะ แขวงทุ่งสองห้อง เขตหลักสี่ กรุงเทพมหานคร 10210',
                'manager' => 'นายชาญชัย ขนส่ง',
                'company_id' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'BR-015',
                'name' => 'สาขาแหลมฉบัง',
                'phone' => '038-567-890',
                'email' => 'laemchabang@company6.co.th',
                'address' => '890 ถนนท่าเรือแหลมฉบัง ตำบลทุ่งสุขลา อำเภอศรีราชา ชลบุรี 20230',
                'manager' => 'นายสมศักดิ์ เดินเรือ',
                'company_id' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'BR-016',
                'name' => 'สาขาคลองเตย',
                'phone' => '02-567-8902',
                'email' => 'klongtoey@company6.co.th',
                'address' => '123 ถนนท่าเรือ แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110',
                'manager' => 'นายท่าเรือ กรุงเทพ',
                'company_id' => 6,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 7 ====================
            [
                'code' => 'BR-017',
                'name' => 'สำนักงานใหญ่',
                'phone' => '076-345-678',
                'email' => 'info@company7.co.th',
                'address' => '890 ถนนเทพกระษัตรี ตำบลรัษฎา อำเภอเมืองภูเก็ต ภูเก็ต 83000',
                'manager' => 'นายเขียว สะอาด',
                'company_id' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'BR-018',
                'name' => 'สาขากระบี่',
                'phone' => '075-345-678',
                'email' => 'krabi@company7.co.th',
                'address' => '456 ถนนมหาราช ตำบลกระบี่ใหญ่ อำเภอเมืองกระบี่ กระบี่ 81000',
                'manager' => 'นายทะเล ใต้',
                'company_id' => 7,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 8 ====================
            [
                'code' => 'BR-019',
                'name' => 'สำนักงานใหญ่',
                'phone' => '044-234-567',
                'email' => 'info@company8.co.th',
                'address' => '321 ถนนมิตรภาพ ตำบลในเมือง อำเภอเมืองนครราชสีมา นครราชสีมา 30000',
                'manager' => 'นายเจริญ ทรัพย์มาก',
                'company_id' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'BR-020',
                'name' => 'สาขาบุรีรัมย์',
                'phone' => '044-234-568',
                'email' => 'buriram@company8.co.th',
                'address' => '123 ถนนจิระ ตำบลในเมือง อำเภอเมืองบุรีรัมย์ บุรีรัมย์ 31000',
                'manager' => 'นางสาวบุรีรัมย์ แข่งรถ',
                'company_id' => 8,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 9 ====================
            [
                'code' => 'BR-021',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-008-8000',
                'email' => 'thailand@company9.com',
                'address' => '999 อาคารเกษร ทาวเวอร์ ถนนเพลินจิต แขวงลุมพินี เขตปทุมวัน กรุงเทพมหานคร 10330',
                'manager' => 'นายไมโคร ซอฟท์',
                'company_id' => 9,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 10 ====================
            [
                'code' => 'BR-022',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-444-5566',
                'email' => 'info@company10.co.th',
                'address' => '444 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400',
                'manager' => 'นายโปรแกรม ดีบั๊ก',
                'company_id' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'BR-023',
                'name' => 'สาขาเชียงใหม่',
                'phone' => '053-444-556',
                'email' => 'chiangmai@company10.co.th',
                'address' => '222 ถนนห้วยแก้ว ตำบลสุเทพ อำเภอเมืองเชียงใหม่ เชียงใหม่ 50200',
                'manager' => 'นางสาวนภา ฟ้าสวย',
                'company_id' => 10,
                'is_active' => true,
            ],

            // ==================== บริษัทที่ 11-20 ====================
            [
                'code' => 'BR-024',
                'name' => 'สำนักงานใหญ่',
                'phone' => '034-567-890',
                'email' => 'info@company11.co.th',
                'address' => '678 ถนนเพชรเกษม ตำบลอ้อมใหญ่ อำเภอสามพราน นครปฐม 73160',
                'manager' => 'นายสมาน งานดี',
                'company_id' => 11,
                'is_active' => false,
            ],
            [
                'code' => 'BR-025',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-666-7777',
                'email' => 'info@company12.co.th',
                'address' => '555 ถนนสุขุมวิท 21 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพมหานคร 10110',
                'manager' => 'นางสาวดิจิทัล ล้ำสมัย',
                'company_id' => 12,
                'is_active' => true,
            ],
            [
                'code' => 'BR-026',
                'name' => 'สำนักงานใหญ่',
                'phone' => '043-345-678',
                'email' => 'info@company13.co.th',
                'address' => '246 ถนนศรีจันทร์ ตำบลในเมือง อำเภอเมืองขอนแก่น ขอนแก่น 40000',
                'manager' => 'นายก่อสร้าง แข็งแรง',
                'company_id' => 13,
                'is_active' => true,
            ],
            [
                'code' => 'BR-027',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-789-0123',
                'email' => 'info@company14.co.th',
                'address' => '789 ถนนสาทรเหนือ แขวงสีลม เขตบางรัก กรุงเทพมหานคร 10500',
                'manager' => 'นายซื้อขาย ทั่วโลก',
                'company_id' => 14,
                'is_active' => true,
            ],
            [
                'code' => 'BR-028',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-456-7891',
                'email' => 'info@company15.co.th',
                'address' => '345 ถนนพญาไท แขวงถนนพญาไท เขตราชเทวี กรุงเทพมหานคร 10400',
                'manager' => 'นางสาวแล็บ ปลอดเชื้อ',
                'company_id' => 15,
                'is_active' => false,
            ],
            [
                'code' => 'BR-029',
                'name' => 'สำนักงานใหญ่',
                'phone' => '038-123-456',
                'email' => 'info@company16.co.th',
                'address' => '1234 ถนนแหลมฉบัง ตำบลทุ่งสุขลา อำเภอศรีราชา ชลบุรี 20230',
                'manager' => 'นายเรือ ใบสวย',
                'company_id' => 16,
                'is_active' => true,
            ],
            [
                'code' => 'BR-030',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-333-4444',
                'email' => 'info@company17.co.th',
                'address' => '333 ถนนวิภาวดีรังสิต แขวงจอมพล เขตจตุจักร กรุงเทพมหานคร 10900',
                'manager' => 'นายตรวจสอบ คุณภาพ',
                'company_id' => 17,
                'is_active' => true,
            ],
            [
                'code' => 'BR-031',
                'name' => 'สำนักงานใหญ่',
                'phone' => '074-345-678',
                'email' => 'info@company18.co.th',
                'address' => '567 ถนนนครนอก ตำบลบ่อยาง อำเภอเมืองสงขลา สงขลา 90000',
                'manager' => 'นายค้าขาย ใต้สุด',
                'company_id' => 18,
                'is_active' => true,
            ],
            [
                'code' => 'BR-032',
                'name' => 'สาขาหาดใหญ่',
                'phone' => '074-345-679',
                'email' => 'hatyai@company18.co.th',
                'address' => '123 ถนนนิพัทธ์อุทิศ 1 ตำบลหาดใหญ่ อำเภอหาดใหญ่ สงขลา 90110',
                'manager' => 'นางสาวสายลม แดนใต้',
                'company_id' => 18,
                'is_active' => true,
            ],
            [
                'code' => 'BR-033',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-888-9999',
                'email' => 'info@company19.co.th',
                'address' => '888 ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพมหานคร 10400',
                'manager' => 'นายเน็ต แรงมาก',
                'company_id' => 19,
                'is_active' => false,
            ],
            [
                'code' => 'BR-034',
                'name' => 'สำนักงานใหญ่',
                'phone' => '02-111-2222',
                'email' => 'info@company20.co.th',
                'address' => '111 ถนนรามคำแหง แขวงหัวหมาก เขตบางกะปิ กรุงเทพมหานคร 10240',
                'manager' => 'นายบริการ ประทับใจ',
                'company_id' => 20,
                'is_active' => true,
            ],
            [
                'code' => 'BR-035',
                'name' => 'สาขาอุดรธานี',
                'phone' => '042-111-222',
                'email' => 'udon@company20.co.th',
                'address' => '456 ถนนอุดรดุษฎี ตำบลหมากแข้ง อำเภอเมืองอุดรธานี อุดรธานี 41000',
                'manager' => 'นายภาคอีสาน แข็งแรง',
                'company_id' => 20,
                'is_active' => true,
            ],
        ];

        $count = 0;
        foreach ($branches as $branch) {
            // ตรวจสอบว่า company_id มีอยู่จริง
            if ($companies->isNotEmpty() && !$companies->contains('id', $branch['company_id'])) {
                // ถ้าไม่มีบริษัทที่ระบุ ให้ข้าม
                continue;
            }

            Branch::create($branch);
            $count++;
        }

        $this->command->info("✅ BranchSeeder: เพิ่มข้อมูลสาขา {$count} รายการเรียบร้อยแล้ว");

        // แสดงสรุป
        $this->command->table(
            ['รายการ', 'จำนวน'],
            [
                ['สาขาทั้งหมด', Branch::count()],
                ['เปิดใช้งาน', Branch::where('is_active', true)->count()],
                ['ปิดใช้งาน', Branch::where('is_active', false)->count()],
            ]
        );
    }
}
