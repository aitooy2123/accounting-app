<x-app-layout>
    <form action="{{ route('pages.sales_store') }}" method="POST">
        @csrf

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">สร้างใบกำกับภาษี / ใบแจ้งหนี้</h1>
                <p class="text-sm text-gray-500">กรอกข้อมูลเพื่อออกเอกสารการขายใหม่</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('pages.sales') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
                    ยกเลิก
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200">
                    <i class="fas fa-save mr-2"></i> บันทึกเอกสาร
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-2 mb-4 text-blue-600 font-bold text-lg">
                        <i class="fas fa-user-circle"></i>
                        <span>ข้อมูลลูกค้า</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">ชื่อลูกค้า / บริษัท</label>
                            <input type="text" name="customer_name" required
                                class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="พิมพ์ชื่อเพื่อค้นหาหรือระบุชื่อลูกค้า">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">เลขประจำตัวผู้เสียภาษี</label>
                            <input type="text" name="tax_id"
                                class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="0123456789012">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">ที่อยู่ลูกค้า</label>
                            <textarea name="address" rows="1"
                                class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="ที่อยู่ในการออกเอกสาร"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4 text-blue-600 font-bold text-lg">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-boxes"></i>
                            <span>รายการสินค้า/บริการ</span>
                        </div>
                        <button type="button" class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-lg hover:bg-blue-100 transition-all">
                            + เพิ่มแถว
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 rounded-lg">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-left">รายละเอียด</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center w-24">จำนวน</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32">ราคา/หน่วย</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32">รวมเงิน</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr>
                                    <td class="py-4 pr-4">
                                        <input type="text" name="items[0][desc]" class="w-full border-none focus:ring-0 text-sm p-0" placeholder="ชื่อสินค้าหรือบริการ...">
                                    </td>
                                    <td class="py-4 px-4">
                                        <input type="number" name="items[0][qty]" value="1" class="w-full border-none focus:ring-0 text-sm text-center p-0">
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <input type="number" name="items[0][price]" value="0.00" class="w-full border-none focus:ring-0 text-sm text-right p-0">
                                    </td>
                                    <td class="py-4 px-4 text-right text-sm font-bold text-gray-700">0.00</td>
                                    <td class="py-4 text-right">
                                        <button type="button" class="text-gray-300 hover:text-red-500"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-2 mb-4 text-blue-600 font-bold text-lg">
                        <i class="fas fa-file-alt"></i>
                        <span>ข้อมูลเอกสาร</span>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">เลขที่เอกสาร</label>
                            <input type="text" name="doc_no" value="Auto" disabled
                                class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-400 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">วันที่ออกเอกสาร</label>
                            <input type="date" name="doc_date" value="{{ date('Y-m-d') }}"
                                class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">ครบกำหนดชำระ</label>
                            <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-blue-600 p-6 rounded-2xl shadow-lg text-white">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm opacity-80">
                            <span>รวมเป็นเงิน</span>
                            <span>0.00</span>
                        </div>
                        <div class="flex justify-between text-sm opacity-80">
                            <span>ภาษีมูลค่าเพิ่ม (7%)</span>
                            <span>0.00</span>
                        </div>
                        <div class="border-t border-blue-500 pt-3 flex justify-between items-end">
                            <span class="font-bold">ยอดเงินสุทธิ</span>
                            <div class="text-right">
                                <span class="text-xs block opacity-80">THB</span>
                                <span class="text-3xl font-bold">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">หมายเหตุ</label>
                    <textarea name="note" rows="3"
                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="ระบุหมายเหตุแนบท้ายเอกสาร..."></textarea>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
