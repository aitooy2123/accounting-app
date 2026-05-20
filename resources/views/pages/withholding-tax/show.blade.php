{{-- resources/views/pages/withholding-tax/show.blade.php --}}
<x-app-layout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">รายละเอียดหนังสือรับรองหัก ณ ที่จ่าย</h1>
            <p class="text-sm text-gray-500 font-kanit">แสดงข้อมูลการหักภาษี ณ ที่จ่ายสำหรับค่าใช้จ่าย</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('withholding-tax.index') }}"
               class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> กลับรายการ
            </a>
            <a href="{{ route('withholding-tax.edit', $withholdingTax) }}"
               class="px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-yellow-200 font-kanit">
                <i class="fas fa-edit mr-2"></i> แก้ไข
            </a>
            <button type="button" onclick="confirmDelete()"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-red-200 font-kanit">
                <i class="fas fa-trash-alt mr-2"></i> ลบ
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- ข้อมูลหลัก --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center space-x-2 mb-6 text-blue-600 font-bold text-lg border-b pb-4 font-kanit">
                    <i class="fas fa-receipt"></i>
                    <span>รายละเอียดใบหัก ณ ที่จ่าย</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลขที่ใบหัก ณ ที่จ่าย</label>
                        <div class="text-sm font-medium text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{ $withholdingTax->withholding_number }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">วันที่ออกเอกสาร</label>
                        <div class="text-sm font-medium text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{ $withholdingTax->date->format('d/m/Y') }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผู้จำหน่าย / บริษัท</label>
                        <div class="text-sm font-medium text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{ $withholdingTax->expense->company->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลขที่ใบกำกับภาษี</label>
                        <div class="text-sm font-medium text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{ $withholdingTax->invoice_number ?? '-' }}
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">หมายเหตุ</label>
                        <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100 min-h-[80px] whitespace-pre-wrap">
                            {{ $withholdingTax->remark ?? 'ไม่มีหมายเหตุ' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar ขวา: สรุปยอดภาษี --}}
        <div class="space-y-6">
            <div class="bg-blue-600 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden font-kanit">
                <div class="absolute -right-4 -top-4 opacity-10 text-8xl">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="space-y-5 relative z-10">
                    <div>
                        <label class="block text-xs opacity-80 mb-1">ยอดเงินก่อนหัก (บาท)</label>
                        <div class="text-2xl font-bold text-right">
                            {{ number_format($withholdingTax->amount_before_withholding, 2) }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs opacity-80 mb-1">อัตราหัก ณ ที่จ่าย</label>
                        <div class="text-xl font-bold text-right">
                            {{ number_format($withholdingTax->withholding_rate, 2) }}%
                        </div>
                    </div>

                    <div class="border-t border-blue-400/50 pt-4 mt-2">
                        <div class="flex justify-between text-sm opacity-80 mb-1">
                            <span>จำนวนภาษีที่หัก</span>
                            <span class="text-xl font-bold">{{ number_format($withholdingTax->withholding_amount, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-end pt-2">
                            <div>
                                <span class="block text-xs opacity-70">ยอดสุทธิหลังหัก</span>
                                <span class="text-3xl font-bold tracking-tight">
                                    {{ number_format($withholdingTax->amount_before_withholding - $withholdingTax->withholding_amount, 2) }}
                                </span>
                            </div>
                            <span class="text-xs font-bold bg-blue-500 px-2 py-1 rounded-md uppercase">THB</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ข้อมูลเพิ่มเติม (ผู้บันทึก, วันที่แก้ไข) --}}
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-xs text-gray-500 space-y-2">
                <div class="flex justify-between">
                    <span>ค่าใช้จ่ายอ้างอิง:</span>
                    <span class="text-blue-600">
                        <a href="{{ route('expenses.show', $withholdingTax->expense_id) }}" class="hover:underline">
                            {{ $withholdingTax->expense->doc_no ?? '#' }}
                        </a>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>สร้างเมื่อ:</span>
                    <span>{{ $withholdingTax->created_at ? $withholdingTax->created_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>แก้ไขล่าสุด:</span>
                    <span>{{ $withholdingTax->updated_at ? $withholdingTax->updated_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ฟอร์มลบ (ซ่อน) --}}
    <form id="delete-form" action="{{ route('withholding-tax.destroy', $withholdingTax) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete() {
            if (confirm('คุณต้องการลบเอกสารหัก ณ ที่จ่ายนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถกู้คืนได้')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</x-app-layout>
