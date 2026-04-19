<x-app-layout>
    <form action="{{ route('pages.sales_update', $sale->id) }}" method="POST" class="font-kanit">
        @csrf
        @method('PUT')

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">แก้ไขเอกสาร: <span class="text-blue-600">{{ $sale->doc_no }}</span></h1>
                <p class="text-sm text-gray-500">แก้ไขรายละเอียดและรายการสินค้าในใบแจ้งหนี้</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('pages.sales') }}" class="px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm">ยกเลิก</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">บันทึกการแก้ไข</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">ลูกค้า</label>
                            <select name="customer_id" class="w-full rounded-xl border-gray-200 mt-1 focus:ring-blue-500">
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ $sale->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">สถานะเอกสาร</label>
                            <select name="status" class="w-full rounded-xl border-gray-200 mt-1 focus:ring-blue-500">
                                <option value="ค้างชำระ" {{ $sale->status == 'ค้างชำระ' ? 'selected' : '' }}>ค้างชำระ</option>
                                <option value="ชำระแล้ว" {{ $sale->status == 'ชำระแล้ว' ? 'selected' : '' }}>ชำระแล้ว</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between mb-4 items-center">
                        <span class="font-bold text-blue-600"><i class="fas fa-list mr-2"></i>รายการสินค้า</span>
                        <button type="button" onclick="addRow()" class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg font-bold hover:bg-blue-100">+ เพิ่มรายการ</button>
                    </div>
                    <table class="w-full" id="itemsTable">
                        <tbody id="item-tbody">
                            @foreach($sale->items as $index => $item)
                            <tr class="item-row">
                                <td class="pb-3 pr-2">
                                    <input type="text" name="items[{{ $index }}][desc]" value="{{ $item->description }}" required class="w-full border-gray-200 rounded-xl text-sm" placeholder="รายละเอียด...">
                                </td>
                                <td class="w-24 pb-3 px-2">
                                    <input type="number" name="items[{{ $index }}][qty]" value="{{ number_format($item->quantity, 0) }}" oninput="calculate()" class="qty w-full border-gray-200 rounded-xl text-center text-sm font-bold">
                                </td>
                                <td class="w-32 pb-3 px-2">
                                    <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item->unit_price }}" oninput="calculate()" class="price w-full border-gray-200 rounded-xl text-right text-sm font-bold text-blue-600">
                                </td>
                                <td class="w-10 pb-3 text-right">
                                    <button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-blue-600 p-8 rounded-[2rem] text-white shadow-xl relative overflow-hidden">
                    <div class="relative z-10 space-y-4">
                        <div class="flex justify-between opacity-80 text-sm"><span>ยอดรวมสินค้า</span><span id="subtotal">{{ number_format($sale->subtotal, 2) }}</span></div>
                        <div class="flex justify-between opacity-80 text-sm"><span>ภาษี (7%)</span><span id="vat">{{ number_format($sale->vat, 2) }}</span></div>
                        <div class="border-t border-blue-400 pt-4 mt-4 flex justify-between items-end">
                            <div>
                                <p class="text-xs opacity-70 font-light">ยอดรวมสุทธิ</p>
                                <p class="text-4xl font-bold" id="total">{{ number_format($sale->total, 2) }}</p>
                            </div>
                            <span class="bg-blue-500/50 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-widest">THB</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">หมายเหตุ</label>
                    <textarea name="note" rows="4" class="w-full border-gray-200 rounded-xl mt-2 text-sm focus:ring-blue-500" placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี)...">{{ $sale->note }}</textarea>
                </div>
            </div>
        </div>
    </form>

    <script>
        let rowIdx = {{ $sale->items->count() }};

        function addRow() {
            const html = `
                <tr class="item-row">
                    <td class="pb-3 pr-2"><input type="text" name="items[${rowIdx}][desc]" required class="w-full border-gray-200 rounded-xl text-sm" placeholder="รายละเอียด..."></td>
                    <td class="w-24 pb-3 px-2"><input type="number" name="items[${rowIdx}][qty]" value="1" oninput="calculate()" class="qty w-full border-gray-200 rounded-xl text-center text-sm font-bold"></td>
                    <td class="w-32 pb-3 px-2"><input type="number" step="0.01" name="items[${rowIdx}][price]" value="0" oninput="calculate()" class="price w-full border-gray-200 rounded-xl text-right text-sm font-bold text-blue-600"></td>
                    <td class="w-10 pb-3 text-right"><button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button></td>
                </tr>`;
            document.getElementById('item-tbody').insertAdjacentHTML('beforeend', html);
            rowIdx++;
            calculate();
        }

        function removeRow(btn) {
            const rows = document.querySelectorAll('.item-row');
            if(rows.length > 1) {
                btn.closest('tr').remove();
                calculate();
            } else {
                alert("ต้องมีอย่างน้อย 1 รายการครับ");
            }
        }

        function calculate() {
            let sub = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const q = parseFloat(row.querySelector('.qty').value) || 0;
                const p = parseFloat(row.querySelector('.price').value) || 0;
                sub += q * p;
            });
            const v = sub * 0.07;
            document.getElementById('subtotal').innerText = sub.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('vat').innerText = v.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('total').innerText = (sub + v).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
    </script>
</x-app-layout>
