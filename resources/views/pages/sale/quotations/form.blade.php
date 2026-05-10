{{-- resources/views/sales/quotations/form.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-kanit text-2xl font-bold text-gray-800 leading-tight">
            <i class="fas fa-{{ isset($quotation) ? 'edit' : 'plus-circle' }} text-blue-600 mr-2"></i>
            {{ isset($quotation) ? 'แก้ไขใบเสนอราคา' : 'สร้างใบเสนอราคาใหม่' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ isset($quotation) ? route('sales.quotations.update', $quotation->id) : route('sales.quotations.store') }}" method="POST" class="space-y-6">
                @csrf
                @if(isset($quotation)) @method('PUT') @endif

                {{-- ข้อมูลผู้ขาย --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 font-kanit mb-4 border-b pb-3">
                        <i class="fas fa-store text-blue-500 mr-2"></i> ข้อมูลผู้ขาย
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">ชื่อบริษัท <span class="text-red-500">*</span></label>
                            <input type="text" value="บริษัท ตัวอย่าง เทคโนโลยี จำกัด" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">เลขประจำตัวผู้เสียภาษี</label>
                            <input type="text" value="0-1234-56789-01-2" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">ที่อยู่</label>
                            <textarea rows="2" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">123/456 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400</textarea>
                        </div>
                    </div>
                </div>

                {{-- ข้อมูลลูกค้า --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 font-kanit mb-4 border-b pb-3">
                        <i class="fas fa-user-tie text-blue-500 mr-2"></i> ข้อมูลลูกค้า (ผู้ซื้อ)
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">ชื่อบริษัท/ลูกค้า <span class="text-red-500">*</span></label>
                            <input type="text" placeholder="ระบุชื่อลูกค้า" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">เลขประจำตัวผู้เสียภาษี</label>
                            <input type="text" placeholder="ถ้ามี" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">ที่อยู่</label>
                            <textarea rows="2" placeholder="ที่อยู่สำหรับออกเอกสาร" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">ผู้ติดต่อ</label>
                            <input type="text" placeholder="ชื่อ-นามสกุล" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">อีเมล</label>
                            <input type="email" placeholder="email@example.com" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                        </div>
                    </div>
                </div>

                {{-- รายการสินค้า/บริการ --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="quotationItems()">
                    <div class="flex items-center justify-between mb-4 border-b pb-3">
                        <h3 class="text-lg font-bold text-gray-800 font-kanit">
                            <i class="fas fa-list-ul text-blue-500 mr-2"></i> รายการสินค้า/บริการ
                        </h3>
                        <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-all font-kanit">
                            <i class="fas fa-plus mr-1"></i> เพิ่มรายการ
                        </button>
                    </div>

                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-12 gap-2 mb-3 items-start p-3 bg-gray-50/50 rounded-xl relative group">
                            <span class="col-span-1 text-sm text-gray-400 font-kanit pt-2" x-text="index + 1"></span>
                            <div class="col-span-4">
                                <input type="text" x-model="item.description" placeholder="ชื่อสินค้า/บริการ" class="w-full border-gray-200 rounded-lg text-sm font-kanit focus:ring-1 focus:ring-blue-100">
                            </div>
                            <div class="col-span-1">
                                <input type="number" x-model="item.quantity" placeholder="จำนวน" min="1" class="w-full border-gray-200 rounded-lg text-sm text-center font-kanit focus:ring-1 focus:ring-blue-100">
                            </div>
                            <div class="col-span-2">
                                <input type="text" x-model="item.unit" placeholder="หน่วยนับ" class="w-full border-gray-200 rounded-lg text-sm text-center font-kanit focus:ring-1 focus:ring-blue-100">
                            </div>
                            <div class="col-span-2">
                                <input type="number" x-model="item.price" placeholder="ราคาต่อหน่วย" step="0.01" class="w-full border-gray-200 rounded-lg text-sm text-right font-kanit focus:ring-1 focus:ring-blue-100">
                            </div>
                            <div class="col-span-1 text-right">
                                <span class="text-sm font-bold text-gray-700 font-kanit pt-2 block" x-text="formatMoney(item.quantity * item.price)"></span>
                            </div>
                            <div class="col-span-1 text-center">
                                <button type="button" @click="removeItem(index)" class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- สรุปยอด --}}
                    <div class="flex justify-end pt-4 border-t border-gray-100 mt-4">
                        <div class="space-y-2 w-64">
                            <div class="flex justify-between text-sm font-kanit">
                                <span class="text-gray-500">ยอดรวม:</span>
                                <span class="font-medium text-gray-700" x-text="formatMoney(totalAmount)"></span>
                            </div>
                            <div class="flex justify-between text-sm font-kanit">
                                <span class="text-gray-500">ภาษีมูลค่าเพิ่ม (7%):</span>
                                <span class="font-medium text-gray-700" x-text="formatMoney(vat)"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t-2 border-gray-300">
                                <span class="text-lg font-bold text-gray-800 font-kanit">ยอดรวมสุทธิ:</span>
                                <span class="text-lg font-black text-blue-600 font-kanit" x-text="formatMoney(grandTotal)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- เงื่อนไขเพิ่มเติม --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i> วันหมดอายุใบเสนอราคา
                        </label>
                        <input type="date" class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">
                            <i class="fas fa-clock text-gray-400 mr-1"></i> เครดิต (วัน)
                        </label>
                        <select class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm">
                            <option value="0">ชำระทันที</option>
                            <option value="15">15 วัน</option>
                            <option value="30" selected>30 วัน</option>
                            <option value="45">45 วัน</option>
                            <option value="60">60 วัน</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 mb-1 font-kanit">หมายเหตุ / ขอบเขตงาน</label>
                        <textarea rows="3" placeholder="ระบุเงื่อนไขพิเศษ ขอบเขตการทำงาน หรือข้อมูลเพิ่มเติม..." class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 font-kanit text-sm"></textarea>
                    </div>
                </div>

                {{-- ปุ่มบันทึก --}}
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('sales.quotations.index') }}" class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 text-sm font-bold font-kanit transition-all">
                        ยกเลิก
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-green-200/50 hover:shadow-green-300/50 transform hover:-translate-y-0.5 font-kanit">
                        <i class="fas fa-save mr-2"></i> บันทึกใบเสนอราคา
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    // สคริปต์ Alpine.js สำหรับจัดการรายการ
    function quotationItems() {
        return {
            items: [
                { description: '', quantity: 1, unit: 'หน่วย', price: 0 },
            ],
            addItem() {
                this.items.push({ description: '', quantity: 1, unit: 'หน่วย', price: 0 });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            get totalAmount() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
            },
            get vat() {
                return this.totalAmount * 0.07;
            },
            get grandTotal() {
                return this.totalAmount + this.vat;
            },
            formatMoney(amount) {
                return '฿' + Number(amount).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }
    }
</script>
