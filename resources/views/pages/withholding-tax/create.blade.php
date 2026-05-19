{{-- resources/views/pages/withholding-tax/create.blade.php --}}
<x-app-layout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">เพิ่มหนังสือรับรองหัก ณ ที่จ่าย</h1>
            <p class="text-sm text-gray-500 font-kanit">บันทึกข้อมูลการหักภาษี ณ ที่จ่ายสำหรับคู่ค้า</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('withholding-tax.index') }}"
               class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> กลับรายการ
            </a>
        </div>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                <span class="font-medium font-kanit">เกิดข้อผิดพลาด กรุณาตรวจสอบข้อมูล</span>
            </div>
            <ul class="mt-2 ml-8 list-disc text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- ฟอร์มหลัก --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('withholding-tax.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-2 mb-6 text-blue-600 font-bold text-lg border-b pb-4 font-kanit">
                        <i class="fas fa-receipt"></i>
                        <span>รายละเอียดใบหัก ณ ที่จ่าย</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- เลขที่ใบหัก ณ ที่จ่าย --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider required:after:content-['*'] after:ml-0.5 after:text-red-500">
                                เลขที่ใบหัก ณ ที่จ่าย
                            </label>
                            <input type="text" name="withholding_no" id="withholding_no"
                                   value="{{ old('withholding_no') }}"
                                   class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('withholding_no') border-red-500 @enderror"
                                   placeholder="WT-2024-00001" required>
                            @error('withholding_no')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- วันที่ออกเอกสาร --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider required:after:content-['*'] after:ml-0.5 after:text-red-500">
                                วันที่ออกเอกสาร
                            </label>
                            <input type="date" name="withholding_date" id="withholding_date"
                                   value="{{ old('withholding_date', date('Y-m-d')) }}"
                                   class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('withholding_date') border-red-500 @enderror"
                                   required>
                            @error('withholding_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ผู้จำหน่าย --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider required:after:content-['*'] after:ml-0.5 after:text-red-500">
                                ผู้จำหน่าย / บริษัท
                            </label>
                            <select name="company_id" id="company_id"
                                    class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 @error('company_id') border-red-500 @enderror"
                                    required>
                                <option value="">-- เลือกผู้จำหน่าย --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- เลขที่ใบกำกับภาษี --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">
                                เลขที่ใบกำกับภาษี
                            </label>
                            <input type="text" name="invoice_no" id="invoice_no"
                                   value="{{ old('invoice_no') }}"
                                   class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('invoice_no') border-red-500 @enderror"
                                   placeholder="INV-2024-00123">
                            @error('invoice_no')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- หมายเหตุ --}}
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">
                                หมายเหตุ
                            </label>
                            <textarea name="remark" id="remark" rows="3"
                                      class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('remark') border-red-500 @enderror"
                                      placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี)">{{ old('remark') }}</textarea>
                            @error('remark')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ส่วนคำนวณภาษี --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-2 mb-6 text-blue-600 font-bold text-lg border-b pb-4 font-kanit">
                        <i class="fas fa-calculator"></i>
                        <span>รายละเอียดภาษีหัก ณ ที่จ่าย</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider required:after:content-['*'] after:ml-0.5 after:text-red-500">
                                ยอดก่อนหัก (Tax Base)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">฿</span>
                                <input type="number" step="0.01" name="tax_base" id="tax_base"
                                       value="{{ old('tax_base') }}"
                                       class="w-full pl-8 pr-3 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('tax_base') border-red-500 @enderror"
                                       placeholder="0.00" required>
                            </div>
                            @error('tax_base')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider required:after:content-['*'] after:ml-0.5 after:text-red-500">
                                อัตราหัก ณ ที่จ่าย (%)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" name="tax_rate" id="tax_rate"
                                       value="{{ old('tax_rate') }}"
                                       class="w-full pr-8 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 @error('tax_rate') border-red-500 @enderror"
                                       placeholder="3.00" required>
                                <span class="absolute right-3 top-3 text-gray-400">%</span>
                            </div>
                            @error('tax_rate')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider required:after:content-['*'] after:ml-0.5 after:text-red-500">
                                จำนวนภาษีที่หัก
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">฿</span>
                                <input type="number" step="0.01" name="tax_amount" id="tax_amount"
                                       value="{{ old('tax_amount') }}"
                                       class="w-full pl-8 pr-3 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl cursor-not-allowed"
                                       readonly required>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">คำนวณอัตโนมัติจากยอดก่อนหักและอัตรา</p>
                            @error('tax_amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ปุ่มบันทึก --}}
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('withholding-tax.index') }}"
                       class="px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all">
                        ยกเลิก
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>

        {{-- Sidebar ขวา: สรุปยอดภาษี (live preview) --}}
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden font-kanit">
                <div class="absolute -right-4 -top-4 opacity-10 text-8xl">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="space-y-5 relative z-10">
                    <div class="text-center border-b border-blue-400/50 pb-3">
                        <span class="text-xs opacity-80">สรุปภาษีหัก ณ ที่จ่าย</span>
                        <div class="text-3xl font-bold mt-1" id="previewNetAmount">0.00</div>
                        <span class="text-xs opacity-70">ยอดสุทธิหลังหัก</span>
                    </div>
                    <div>
                        <label class="block text-xs opacity-80 mb-1">ยอดเงินก่อนหัก</label>
                        <div class="text-xl font-bold text-right" id="previewBase">0.00</div>
                    </div>
                    <div>
                        <label class="block text-xs opacity-80 mb-1">อัตราหัก ณ ที่จ่าย</label>
                        <div class="text-xl font-bold text-right" id="previewRate">0.00%</div>
                    </div>
                    <div class="border-t border-blue-400/50 pt-4 mt-2">
                        <div class="flex justify-between text-sm opacity-80 mb-1">
                            <span>จำนวนภาษีที่หัก</span>
                            <span class="text-xl font-bold" id="previewTaxAmount">0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-xs text-gray-500">
                <div class="flex items-center space-x-2 mb-2 text-gray-600">
                    <i class="fas fa-info-circle"></i>
                    <span class="font-medium">เคล็ดลับ</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-gray-500">
                    <li>อัตราภาษีหัก ณ ที่จ่ายทั่วไปคือ 1%, 3%, 5%, 10%, 15%</li>
                    <li>จำนวนภาษี = ยอดก่อนหัก × อัตรา ÷ 100</li>
                    <li>สามารถเพิ่มใบกำกับภาษีเพื่ออ้างอิงได้</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // DOM elements
        const baseInput = document.getElementById('tax_base');
        const rateInput = document.getElementById('tax_rate');
        const taxAmountInput = document.getElementById('tax_amount');

        // Preview elements
        const previewBase = document.getElementById('previewBase');
        const previewRate = document.getElementById('previewRate');
        const previewTaxAmount = document.getElementById('previewTaxAmount');
        const previewNetAmount = document.getElementById('previewNetAmount');

        function calculateTax() {
            let base = parseFloat(baseInput.value) || 0;
            let rate = parseFloat(rateInput.value) || 0;
            let taxAmount = (base * rate) / 100;

            // Update readonly field
            taxAmountInput.value = taxAmount.toFixed(2);

            // Update preview sidebar
            previewBase.innerText = base.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            previewRate.innerText = rate.toFixed(2) + '%';
            previewTaxAmount.innerText = taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            previewNetAmount.innerText = (base - taxAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        baseInput.addEventListener('input', calculateTax);
        rateInput.addEventListener('input', calculateTax);

        // Initial calculation if old values exist
        calculateTax();

        // Optional: Auto-format number fields when typing (remove non-digits except dot)
        function formatNumberInput(e) {
            let value = e.target.value;
            value = value.replace(/[^0-9.]/g, '');
            if ((value.match(/\./g) || []).length > 1) {
                value = value.slice(0, value.lastIndexOf('.'));
            }
            e.target.value = value;
        }
        baseInput.addEventListener('input', formatNumberInput);
        rateInput.addEventListener('input', formatNumberInput);
    </script>
</x-app-layout>
