{{-- resources/views/sales/quotations/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="font-kanit text-2xl font-bold text-gray-800 leading-tight">
                <i class="fas fa-file-invoice text-blue-600 mr-2"></i> จัดการใบเสนอราคา
            </h2>
            <a href="{{ route('sales.quotations.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-green-200/50 hover:shadow-green-300/50 transform hover:-translate-y-0.5 font-kanit mt-3 md:mt-0">
                <i class="fas fa-plus-circle mr-2"></i> สร้างใบเสนอราคาใหม่
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- แถบค้นหาและกรอง --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="ค้นหาด้วยเลขที่, ชื่อลูกค้า, หรือผู้ติดต่อ..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all text-sm font-kanit">
                    </div>
                    <select class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all font-kanit text-gray-600">
                        <option value="">สถานะ: ทั้งหมด</option>
                        <option value="draft">แบบร่าง</option>
                        <option value="sent">ส่งแล้ว</option>
                        <option value="approved">อนุมัติแล้ว</option>
                        <option value="expired">หมดอายุ</option>
                        <option value="converted">แปลงเป็นใบแจ้งหนี้แล้ว</option>
                    </select>
                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-kanit transition-all">
                        <i class="fas fa-redo-alt mr-1"></i> รีเซ็ต
                    </button>
                </div>
            </div>

            {{-- ตารางรายการ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-kanit">เลขที่เอกสาร</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-kanit">วันที่ออก</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-kanit">ลูกค้า</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider font-kanit">ยอดรวมสุทธิ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider font-kanit">วันหมดอายุ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider font-kanit">สถานะ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider font-kanit">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            {{-- ตัวอย่างข้อมูล --}}
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('sales.quotations.show', 1) }}" class="text-blue-600 hover:text-blue-800 font-bold font-kanit text-sm">
                                        QT-2567-0001
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-kanit">25 ต.ค. 2567</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-800 font-kanit">บริษัท ตัวอย่าง จำกัด</div>
                                    <div class="text-xs text-gray-400 font-kanit">คุณสมชาย ติดต่อ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-700 font-kanit">฿125,000.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 font-kanit">9 พ.ย. 2567</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 font-kanit">
                                        <i class="fas fa-check-circle mr-1"></i> อนุมัติแล้ว
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('sales.quotations.show', 1) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.quotations.edit', 1) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete(1)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="ลบ">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        {{-- ปุ่มแปลงเป็นใบแจ้งหนี้ ตามที่ FlowAccount แนะนำ --}}
                                        <form action="{{ route('sales.quotations.convert', 1) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="แปลงเป็นใบแจ้งหนี้">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- แถวที่ 2 --}}
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-bold font-kanit text-sm">QT-2567-0002</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-kanit">26 ต.ค. 2567</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-800 font-kanit">ร้านอาหาร อร่อยดี</div>
                                    <div class="text-xs text-gray-400 font-kanit">คุณสมหญิง ผู้จัดการ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-700 font-kanit">฿45,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 font-kanit">10 พ.ย. 2567</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 font-kanit">
                                        <i class="fas fa-clock mr-1"></i> รอการตอบกลับ
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    {{-- ปุ่มจัดการเหมือนแถวบน --}}
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="#" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"><i class="fas fa-edit"></i></a>
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ส่วนแบ่งหน้า --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500 font-kanit">
                        แสดง 1 ถึง 2 จากทั้งหมด 15 รายการ
                    </div>
                    <div class="flex space-x-1">
                        <button class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 font-kanit">ก่อนหน้า</button>
                        <button class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg font-kanit">1</button>
                        <button class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 font-kanit">2</button>
                        <button class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 font-kanit">3</button>
                        <button class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 font-kanit">ถัดไป</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
