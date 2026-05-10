{{-- resources/views/sales/quotations/show.blade.php --}}
<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- แถบสถานะและการกระทำ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-700 font-kanit">
                        <i class="fas fa-check-circle mr-1"></i> อนุมัติแล้ว
                    </span>
                    <span class="text-sm text-gray-500 font-kanit">
                        <i class="far fa-clock mr-1"></i> หมดอายุ: 9 พฤศจิกายน 2567
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('sales.quotations.edit', $quotation->id ?? 1) }}" class="inline-flex items-center px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-bold rounded-xl transition-all font-kanit">
                        <i class="fas fa-edit mr-2"></i> แก้ไข
                    </a>
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-bold rounded-xl transition-all font-kanit">
                        <i class="fas fa-print mr-2"></i> พิมพ์
                    </button>
                    <button class="inline-flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-bold rounded-xl transition-all font-kanit">
                        <i class="fas fa-envelope mr-2"></i> ส่งอีเมล
                    </button>
                    {{-- ปุ่มแปลงเป็นใบแจ้งหนี้ ตาม FlowAccount --}}
                    <form action="{{ route('sales.quotations.convert', $quotation->id ?? 1) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-green-200/50 font-kanit">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> แปลงเป็นใบแจ้งหนี้
                        </button>
                    </form>
                </div>
            </div>

            {{-- เอกสารใบเสนอราคา --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden print:shadow-none print:border-none" id="printArea">

                {{-- หัวเอกสาร --}}
                <div class="p-8 md:p-10 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6">
                        {{-- ส่วนผู้ขาย --}}
                        <div class="flex-1">
                            <img src="{{ asset('images/logo-placeholder.png') }}" alt="โลโก้บริษัท" class="h-14 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 font-kanit">บริษัท ตัวอย่าง เทคโนโลยี จำกัด</h3>
                            <p class="text-sm text-gray-500 font-kanit mt-1">
                                123/456 ถนนรัชดาภิเษก แขวงดินแดง<br>
                                เขตดินแดง กรุงเทพมหานคร 10400
                            </p>
                            <p class="text-sm text-gray-500 font-kanit mt-2">
                                <span class="font-medium">เลขประจำตัวผู้เสียภาษี:</span> 0-1234-56789-01-2
                            </p>
                            <p class="text-sm text-gray-500 font-kanit">
                                <span class="font-medium">โทร:</span> 02-123-4567 | <span class="font-medium">อีเมล:</span> contact@example.com
                            </p>
                        </div>

                        {{-- ชื่อเอกสาร --}}
                        <div class="text-right">
                            <h1 class="text-3xl md:text-4xl font-black text-blue-600 font-kanit tracking-tight">ใบเสนอราคา</h1>
                            <div class="mt-3 space-y-1">
                                <p class="text-sm font-kanit">
                                    <span class="text-gray-500">เลขที่:</span>
                                    <span class="font-bold text-gray-800">QT-2567-0001</span>
                                </p>
                                <p class="text-sm font-kanit">
                                    <span class="text-gray-500">วันที่ออก:</span>
                                    <span class="font-medium text-gray-700">25 ตุลาคม 2567</span>
                                </p>
                                <p class="text-sm font-kanit">
                                    <span class="text-gray-500">วันหมดอายุ:</span>
                                    <span class="font-medium text-red-600">9 พฤศจิกายน 2567</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ข้อมูลลูกค้า --}}
                <div class="px-8 md:px-10 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider font-kanit mb-2">
                        <i class="fas fa-building mr-1"></i> ข้อมูลลูกค้า (ผู้ซื้อ)
                    </h4>
                    <div class="flex flex-col md:flex-row md:justify-between gap-4">
                        <div>
                            <p class="font-bold text-gray-800 font-kanit">บริษัท ตัวอย่าง จำกัด</p>
                            <p class="text-sm text-gray-500 font-kanit">789 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110</p>
                            <p class="text-sm text-gray-500 font-kanit mt-1">
                                <span class="font-medium">เลขประจำตัวผู้เสียภาษี:</span> 9-8765-43210-98-7
                            </p>
                        </div>
                        <div class="text-sm text-gray-500 font-kanit">
                            <p><span class="font-medium">ผู้ติดต่อ:</span> คุณสมชาย ใจดี</p>
                            <p><span class="font-medium">โทร:</span> 081-234-5678</p>
                            <p><span class="font-medium">อีเมล:</span> somchai@example.com</p>
                        </div>
                    </div>
                </div>

                {{-- ตารางรายการสินค้า/บริการ --}}
                <div class="p-8 md:p-10">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider font-kanit mb-3">
                        <i class="fas fa-list-ul mr-1"></i> รายการสินค้า/บริการ
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-y-2 border-gray-200">
                                    <th class="py-3 px-2 text-left text-xs font-bold text-gray-500 uppercase font-kanit w-10">#</th>
                                    <th class="py-3 px-2 text-left text-xs font-bold text-gray-500 uppercase font-kanit">รายการ</th>
                                    <th class="py-3 px-2 text-center text-xs font-bold text-gray-500 uppercase font-kanit w-20">จำนวน</th>
                                    <th class="py-3 px-2 text-center text-xs font-bold text-gray-500 uppercase font-kanit w-20">หน่วยนับ</th>
                                    <th class="py-3 px-2 text-right text-xs font-bold text-gray-500 uppercase font-kanit w-32">ราคาต่อหน่วย</th>
                                    <th class="py-3 px-2 text-right text-xs font-bold text-gray-500 uppercase font-kanit w-32">มูลค่ารวม</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr class="hover:bg-blue-50/20">
                                    <td class="py-3 px-2 text-sm text-gray-500 font-kanit">1</td>
                                    <td class="py-3 px-2">
                                        <p class="text-sm font-bold text-gray-800 font-kanit">ออกแบบเว็บไซต์บริษัท (WordPress)</p>
                                        <p class="text-xs text-gray-400 font-kanit">รวมเทมเพลต, 10 หน้า, ติดตั้ง SEO พื้นฐาน</p>
                                    </td>
                                    <td class="py-3 px-2 text-sm text-center text-gray-600 font-kanit">1</td>
                                    <td class="py-3 px-2 text-sm text-center text-gray-600 font-kanit">โปรเจกต์</td>
                                    <td class="py-3 px-2 text-sm text-right text-gray-700 font-kanit">฿80,000.00</td>
                                    <td class="py-3 px-2 text-sm text-right font-bold text-gray-800 font-kanit">฿80,000.00</td>
                                </tr>
                                <tr class="hover:bg-blue-50/20">
                                    <td class="py-3 px-2 text-sm text-gray-500 font-kanit">2</td>
                                    <td class="py-3 px-2">
                                        <p class="text-sm font-bold text-gray-800 font-kanit">ระบบจัดการสมาชิก (Member System)</p>
                                        <p class="text-xs text-gray-400 font-kanit">พัฒนา Custom Plugin,  dashboard ผู้ดูแล</p>
                                    </td>
                                    <td class="py-3 px-2 text-sm text-center text-gray-600 font-kanit">1</td>
                                    <td class="py-3 px-2 text-sm text-center text-gray-600 font-kanit">ระบบ</td>
                                    <td class="py-3 px-2 text-sm text-right text-gray-700 font-kanit">฿45,000.00</td>
                                    <td class="py-3 px-2 text-sm text-right font-bold text-gray-800 font-kanit">฿45,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- สรุปยอด --}}
                <div class="px-8 md:px-10 py-5 bg-gray-50/50 border-t border-gray-100">
                    <div class="flex flex-col items-end space-y-2">
                        <div class="flex justify-between w-64 text-sm font-kanit">
                            <span class="text-gray-500">ยอดรวมก่อนภาษี:</span>
                            <span class="font-medium text-gray-700">฿125,000.00</span>
                        </div>
                        <div class="flex justify-between w-64 text-sm font-kanit">
                            <span class="text-gray-500">ภาษีมูลค่าเพิ่ม (7%):</span>
                            <span class="font-medium text-gray-700">฿8,750.00</span>
                        </div>
                        <div class="flex justify-between w-64 pt-2 border-t-2 border-gray-300">
                            <span class="text-lg font-bold text-gray-800 font-kanit">ยอดรวมสุทธิ:</span>
                            <span class="text-lg font-black text-blue-600 font-kanit">฿133,750.00</span>
                        </div>
                        <p class="text-xs text-gray-400 font-kanit mt-1">* หนึ่งแสนสามหมื่นสามพันเจ็ดร้อยห้าสิบบาทถ้วน</p>
                    </div>
                </div>

                {{-- เงื่อนไขและหมายเหตุ --}}
                <div class="px-8 md:px-10 py-6 border-t border-gray-100 grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider font-kanit mb-2">
                            <i class="fas fa-credit-card mr-1"></i> เงื่อนไขการชำระเงิน
                        </h4>
                        <ul class="space-y-1 text-sm text-gray-600 font-kanit">
                            <li><i class="fas fa-check text-green-500 mr-1.5"></i> เครดิต 30 วัน นับจากวันที่ในใบส่งของ/ใบกำกับภาษี</li>
                            <li><i class="fas fa-check text-green-500 mr-1.5"></i> ชำระโดยการโอนเงินผ่านธนาคาร</li>
                            <li><span class="font-medium">ธนาคาร:</span> กสิกรไทย สาขาเซ็นทรัลพระราม 9</li>
                            <li><span class="font-medium">เลขที่บัญชี:</span> 123-4-56789-0</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider font-kanit mb-2">
                            <i class="fas fa-info-circle mr-1"></i> หมายเหตุ/ขอบเขตงาน
                        </h4>
                        <ul class="space-y-1 text-sm text-gray-600 font-kanit">
                            <li><i class="fas fa-circle text-[4px] text-gray-400 mr-1.5 align-middle"></i> ราคานี้รวมค่าโดเมนและโฮสติ้ง 1 ปี</li>
                            <li><i class="fas fa-circle text-[4px] text-gray-400 mr-1.5 align-middle"></i> ส่งมอบงานภายใน 45 วันหลังอนุมัติใบเสนอราคา</li>
                            <li><i class="fas fa-circle text-[4px] text-gray-400 mr-1.5 align-middle"></i> รับประกันแก้ไขข้อผิดพลาด 90 วัน</li>
                        </ul>
                    </div>
                </div>

                {{-- ผู้ลงนาม --}}
                <div class="px-8 md:px-10 py-6 border-t border-gray-100">
                    <div class="flex justify-end">
                        <div class="text-center">
                            <p class="text-sm font-bold text-gray-700 font-kanit mb-8">ในนาม บริษัท ตัวอย่าง เทคโนโลยี จำกัด</p>
                            <div class="border-b border-gray-300 w-48 mx-auto"></div>
                            <p class="text-sm text-gray-500 font-kanit mt-1">(นายทดสอบ ดีมาก)</p>
                            <p class="text-xs text-gray-400 font-kanit">กรรมการผู้จัดการ</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ปุ่มด้านล่าง --}}
            <div class="text-center">
                <a href="{{ route('sales.quotations.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 font-kanit text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> กลับไปหน้ารายการใบเสนอราคา
                </a>
            </div>
        </div>
    </div>

    {{-- สคริปต์สำหรับยืนยันการลบ --}}
    <script>
        function confirmDelete(id) {
            if (confirm('คุณแน่ใจหรือไม่ที่จะลบใบเสนอราคานี้? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
                // ส่งฟอร์มลบ หรือใช้ fetch
                console.log('ลบใบเสนอราคา ID:', id);
            }
        }
    </script>
</x-app-layout>
