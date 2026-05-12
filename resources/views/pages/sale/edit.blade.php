<x-app-layout>
    {{-- ส่วนแสดง Error Validation --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-sm font-bold text-red-800 font-kanit">เกิดข้อผิดพลาดในการบันทึก:</h3>
                    <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('sales.update', $sale) }}" method="POST" id="salesForm">
        @csrf
        @method('PUT')

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-kanit">
                    แก้ไขใบกำกับภาษี / ใบแจ้งหนี้
                </h1>
                <p class="text-sm text-gray-500 font-kanit">
                    เอกสารเลขที่ #{{ $sale->doc_no }}
                </p>
            </div>

            <div class="flex gap-3 mt-4 md:mt-0 font-kanit">
                <a href="{{ route('sales.index') }}"
                   class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition">
                    ยกเลิก
                </a>

                <a href="{{ route('sales.export', $sale->id) }}"
                   class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-green-100">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </a>

                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-blue-100">
                    <i class="fas fa-save mr-2"></i>
                    อัปเดตเอกสาร
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- คอลัมน์ซ้าย: ข้อมูลลูกค้าและรายการสินค้า --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- ส่วนข้อมูลลูกค้า --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-2 mb-6 text-blue-600 font-bold text-lg border-b pb-4 font-kanit">
                        <i class="fas fa-user-circle"></i>
                        <span>ข้อมูลลูกค้า</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- เลือกลูกค้า --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2">ลูกค้า</label>
                            <select name="customer_id" onchange="updateCustomerInfo(this)"
                                    class="w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-blue-500">
                                <option value="">-- เลือกลูกค้า --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        data-tax="{{ $customer->tax_id }}"
                                        data-address="{{ $customer->address }}"
                                        data-company="{{ $customer->company->name ?? '' }}"
                                        data-company-id="{{ $customer->company_id ?? '' }}"
                                        {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ชื่อบริษัท (Read-only) --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2">บริษัท</label>
                            <input type="text" id="company_name" readonly
                                   value="{{ old('company_name', $sale->customer->company->name ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm py-2.5 text-gray-500">
                            <input type="hidden" name="company_id" id="company_id" value="{{ old('company_id', $sale->company_id) }}">
                        </div>

                        {{-- สาขา --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2">สาขา</label>
                            <select name="branch_id" id="branch_select" class="w-full rounded-xl border-gray-200 text-sm py-2.5">
                                <option value="">สำนักงานใหญ่</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id', $sale->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- เลขประจำตัวผู้เสียภาษี --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2">เลขภาษี</label>
                            <input type="text" name="tax_id" id="tax_id" readonly
                                   value="{{ old('tax_id', $sale->customer->tax_id ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm py-2.5 text-gray-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2">ที่อยู่</label>
                            <input type="text" name="address" id="address" readonly
                                   value="{{ old('address', $sale->customer->address ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm py-2.5 text-gray-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2">หมายเหตุ</label>
                            <textarea name="note" rows="2" class="w-full rounded-xl border-gray-200 text-sm focus:ring-blue-500">{{ old('note', $sale->note) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ส่วนรายการสินค้า --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2 text-blue-600 font-bold text-lg font-kanit">
                            <i class="fas fa-boxes"></i>
                            <span>รายการสินค้า</span>
                        </div>
                        <button type="button" onclick="addRow()"
                                class="text-xs bg-blue-50 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-100 transition font-bold font-kanit">
                            <i class="fas fa-plus mr-1"></i> เพิ่มรายการ
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-2">
                            <thead class="bg-gray-50">
                                <tr class="text-[10px] uppercase text-gray-400 font-bold font-kanit">
                                    <th class="px-4 py-3 text-left">รายละเอียดรายการ</th>
                                    <th class="px-4 py-3 text-center w-24">จำนวน</th>
                                    <th class="px-4 py-3 text-right w-32">ราคา/หน่วย</th>
                                    <th class="px-4 py-3 text-right w-32">รวมเงิน</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="item-tbody">
                                @php
                                    // โหลดค่าเก่าจาก Validation หรือจาก Database
                                    $items = old('items', $sale->items->map(fn($i) => [
                                        'desc' => $i->description,
                                        'qty' => $i->quantity,
                                        'price' => $i->unit_price
                                    ])->toArray());

                                    if(empty($items)) $items = [['desc' => '', 'qty' => 1, 'price' => 0]];
                                @endphp

                                @foreach ($items as $index => $item)
                                    <tr class="bg-white border border-gray-100 rounded-lg shadow-sm item-row group transition-all hover:border-blue-200">
                                        <td class="py-3 px-4">
                                            <input type="text" name="items[{{ $index }}][desc]" value="{{ $item['desc'] }}"
                                                   class="w-full border-none focus:ring-0 text-sm p-0 placeholder-gray-300" placeholder="ระบุรายการสินค้า...">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" min="0.01" step="any" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}"
                                                   oninput="calculateTotal()" class="qty-input w-full border-none focus:ring-0 text-center text-sm font-semibold">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item['price'] }}"
                                                   oninput="calculateTotal()" class="price-input w-full border-none focus:ring-0 text-right text-sm font-semibold">
                                        </td>
                                        <td class="py-3 px-4 text-right text-sm font-bold text-gray-700 row-total">0.00</td>
                                        <td class="py-3 text-center">
                                            <button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500 transition px-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- คอลัมน์ขวา: ข้อมูลเอกสาร สรุปยอด และสถานะ --}}
            <div class="space-y-6">

                {{-- ข้อมูลเอกสาร --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center space-x-2 mb-4 text-blue-600 font-bold text-lg font-kanit">
                        <i class="fas fa-file-invoice"></i>
                        <span>ข้อมูลเอกสาร</span>
                    </div>
                    <div class="space-y-4 font-kanit">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">วันที่เอกสาร</label>
                            <input type="date" name="doc_date" value="{{ old('doc_date', $sale->doc_date) }}"
                                   class="w-full rounded-xl border-gray-200 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">เงื่อนไขการชำระ (เครดิต)</label>
                            <select name="credit_term" class="w-full rounded-xl border-gray-200">
                                @foreach([0 => 'เงินสด', 7 => '7 วัน', 30 => '30 วัน', 60 => '60 วัน'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('credit_term', $sale->credit_term) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- การคำนวณภาษี --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <div class="mb-3 font-bold text-gray-700 text-sm font-kanit">อัตราภาษี (VAT)</div>
                    <div class="flex flex-wrap gap-4">
                        @foreach([0, 7, 10] as $rate)
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="vat_rate" value="{{ $rate }}" onchange="calculateTotal()"
                                       {{ old('vat_rate', $sale->vat_rate) == $rate ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600 group-hover:text-blue-600">{{ $rate }}%</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- สถานะ --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 font-kanit">
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">สถานะเอกสาร</label>
                    <select name="status" class="w-full rounded-xl border-gray-200 text-sm">
                        <option value="">-- เลือกสถานะ --</option>
                        @foreach(['ชำระแล้ว', 'ค้างชำระ', 'ออกใบเสนอราคา'] as $st)
                            <option value="{{ $st }}" {{ old('status', $sale->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ผังบัญชี --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 font-kanit">
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">ผังบัญชีรายได้</label>
                    <select name="chart_of_account_id" class="w-full rounded-xl border-gray-200 text-sm">
                        <option value="">-- เลือกผังบัญชี --</option>
                        @foreach ($chartOfAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('chart_of_account_id', $sale->chart_of_account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- การ์ดสรุปยอดเงิน (สีน้ำเงิน) --}}
                <div class="bg-blue-600 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden font-kanit">
                    {{-- วงกลมตกแต่ง --}}
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500 rounded-full opacity-20"></div>

                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between text-sm opacity-80">
                            <span>รวมเป็นเงิน (Subtotal)</span>
                            <span id="display-subtotal">0.00</span>
                        </div>
                        <div class="flex justify-between text-sm opacity-80">
                            <span id="vat-label">ภาษีมูลค่าเพิ่ม ({{ old('vat_rate', $sale->vat_rate) }}%)</span>
                            <span id="display-vat">0.00</span>
                        </div>
                        <div class="border-t border-blue-400/50 pt-4 flex justify-between items-end">
                            <span class="text-lg font-bold">ยอดเงินสุทธิ</span>
                            <div class="text-right">
                                <span class="block text-3xl font-black" id="display-total">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
        // กำหนด Index เริ่มต้นสำหรับแถวใหม่
        let rowCount = {{ count($items) }};

        /**
         * อัปเดตข้อมูลลูกค้าเมื่อเลือกรายชื่อ
         */
        function updateCustomerInfo(select) {
            const opt = select.options[select.selectedIndex];
            if(!opt.value) return;

            document.getElementById('tax_id').value = opt.dataset.tax || '';
            document.getElementById('address').value = opt.dataset.address || '';
            document.getElementById('company_name').value = opt.dataset.company || '';
            document.getElementById('company_id').value = opt.dataset.companyId || '';
        }

        /**
         * เพิ่มแถวรายการสินค้าใหม่
         */
        function addRow() {
            const tbody = document.getElementById('item-tbody');
            const row = document.createElement('tr');
            row.className = "bg-white border border-gray-100 rounded-lg shadow-sm item-row group transition-all hover:border-blue-200";

            row.innerHTML = `
                <td class="py-3 px-4">
                    <input type="text" name="items[${rowCount}][desc]" class="w-full border-none focus:ring-0 text-sm p-0 placeholder-gray-300" placeholder="ระบุรายการสินค้า...">
                </td>
                <td class="py-3 px-4">
                    <input type="number" min="0.01" step="any" value="1" name="items[${rowCount}][qty]" oninput="calculateTotal()" class="qty-input w-full border-none focus:ring-0 text-center text-sm font-semibold">
                </td>
                <td class="py-3 px-4">
                    <input type="number" step="0.01" value="0" name="items[${rowCount}][price]" oninput="calculateTotal()" class="price-input w-full border-none focus:ring-0 text-right text-sm font-semibold">
                </td>
                <td class="py-3 px-4 text-right text-sm font-bold text-gray-700 row-total">0.00</td>
                <td class="py-3 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500 transition px-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            rowCount++;
            calculateTotal();
        }

        /**
         * ลบแถวรายการ
         */
        function removeRow(button) {
            if (document.querySelectorAll('.item-row').length <= 1) {
                alert('ต้องมีอย่างน้อย 1 รายการ');
                return;
            }
            button.closest('tr').remove();
            calculateTotal();
        }

        /**
         * คำนวณยอดเงินทั้งหมด (Subtotal, VAT, Total)
         */
        function calculateTotal() {
            let subtotal = 0;

            // คำนวณแต่ละแถว
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const total = qty * price;

                row.querySelector('.row-total').innerText = total.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                subtotal += total;
            });

            // คำนวณ VAT
            const vatRate = parseFloat(document.querySelector('input[name="vat_rate"]:checked')?.value || 0);
            const vatAmount = subtotal * (vatRate / 100);
            const grandTotal = subtotal + vatAmount;

            // แสดงผลในหน้าจอ
            document.getElementById('vat-label').innerText = `ภาษีมูลค่าเพิ่ม (${vatRate}%)`;
            document.getElementById('display-subtotal').innerText = subtotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('display-vat').innerText = vatAmount.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('display-total').innerText = grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
        }

        // เริ่มต้นคำนวณเมื่อหน้าเว็บโหลดเสร็จ
        document.addEventListener('DOMContentLoaded', () => {
            calculateTotal();
        });
    </script>
</x-app-layout>
