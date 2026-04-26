<x-app-layout>
  @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl font-kanit">
      <div class="flex">
        <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
        <div>
          <h3 class="text-sm font-bold text-red-800">เกิดข้อผิดพลาดในการบันทึก:</h3>
          <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  @endif

  <form action="{{ route('sales.update', $sale->id) }}" method="POST" id="salesForm">
    @csrf
    @method('PUT')

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 font-kanit">แก้ไขเอกสาร: <span class="text-blue-600">{{ $sale->doc_no }}</span></h1>
        <p class="text-sm text-gray-500 font-kanit">อัปเดตรายละเอียดและรายการสินค้าในเอกสารการขาย</p>
      </div>
      <div class="flex space-x-3">
        <a href="{{ route('sales.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all font-kanit">ยกเลิก</a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200 font-kanit">
          <i class="fas fa-save mr-2"></i> บันทึกการแก้ไข
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="flex items-center space-x-2 mb-6 text-blue-600 font-bold text-lg border-b pb-4 font-kanit">
            <i class="fas fa-user-circle"></i>
            <span>ระบุรายละเอียดคู่ค้า</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-1">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ชื่อลูกค้า / บริษัท</label>
              <select name="customer_id" onchange="updateCustomerInfo(this)" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5">
                @foreach ($customers as $customer)
                  <option value="{{ $customer->id }}" data-tax="{{ $customer->tax_id }}" data-address="{{ $customer->address }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                    {{ $customer->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลือกสาขา (Branch)</label>
              <select name="branch_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5">
                @foreach ($branches as $branch)
                  <option value="{{ $branch->id }}" {{ $sale->branch_id == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลขประจำตัวผู้เสียภาษี</label>
              <input type="text" name="tax_id" id="tax_id" value="{{ $sale->customer->tax_id ?? '' }}" readonly class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm py-2.5 text-gray-500">
            </div>

            <div class="md:col-span-1">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ที่อยู่จัดส่งเอกสาร</label>
              <input type="text" name="address" id="address" value="{{ $sale->customer->address ?? '' }}" readonly class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm py-2.5 text-gray-500">
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="flex items-center justify-between mb-4 text-blue-600 font-bold text-lg font-kanit">
            <div class="flex items-center space-x-2">
              <i class="fas fa-boxes"></i>
              <span>รายการสินค้า/บริการ</span>
            </div>
            <button type="button" onclick="addRow()" class="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition-all font-bold">
              <i class="fas fa-plus mr-1"></i> เพิ่มรายการ
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full border-separate border-spacing-y-2" id="itemsTable">
              <thead class="bg-gray-50 rounded-lg">
                <tr>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-left tracking-widest">รายละเอียดสินค้า</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center w-24 tracking-widest">จำนวน</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32 tracking-widest">ราคา/หน่วย</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32 tracking-widest">รวมเงิน</th>
                  <th class="w-10"></th>
                </tr>
              </thead>
              <tbody id="item-tbody">
                @foreach ($sale->items as $index => $item)
                  <tr class="bg-white border border-gray-100 rounded-lg shadow-sm item-row">
                    <td class="py-3 px-4">
                      <input type="text" name="items[{{ $index }}][desc]" value="{{ $item->description }}" class="w-full border-none focus:ring-0 text-sm p-0 font-medium" placeholder="ชื่อสินค้าหรือบริการ...">
                    </td>
                    <td class="py-3 px-4">
                      <input type="number" name="items[{{ $index }}][qty]" value="{{ (float) $item->quantity }}" min="1" oninput="calculateTotal()" class="qty-input w-full border-none focus:ring-0 text-sm text-center p-0 font-bold">
                    </td>
                    <td class="py-3 px-4">
                      <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item->unit_price }}" oninput="calculateTotal()" class="price-input w-full border-none focus:ring-0 text-sm text-right p-0 font-bold text-blue-600">
                    </td>
                    <td class="py-3 px-4 text-right text-sm font-bold text-gray-700 row-total">0.00</td>
                    <td class="py-3 text-center">
                      <button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500 transition-colors">
                        <i class="fas fa-trash-alt text-xs"></i>
                      </button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 font-kanit">
          <div class="flex items-center space-x-2 mb-4 text-blue-600 font-bold text-lg">
            <i class="fas fa-file-invoice"></i>
            <span>ข้อมูลทั่วไป</span>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">สถานะ</label>
              <select name="status" class="w-full rounded-xl border-gray-200 text-sm py-2">
                <option value="ค้างชำระ" {{ $sale->status == 'ค้างชำระ' ? 'selected' : '' }}>ค้างชำระ</option>
                <option value="ชำระแล้ว" {{ $sale->status == 'ชำระแล้ว' ? 'selected' : '' }}>ชำระแล้ว</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">วันที่เอกสาร</label>
              <input type="date" name="doc_date" value="{{ $sale->doc_date->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200 text-sm py-2">
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <i class="fas fa-percent text-blue-600"></i>
            <span class="text-sm font-bold text-gray-700">คำนวณภาษี (VAT 7%)</span>
          </div>
          <label class="inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_vat" value="0">

            <input type="checkbox" id="vat_toggle" name="is_vat" value="1" class="sr-only peer" {{ $sale->vat > 0 ? 'checked' : '' }} onchange="calculateTotal()">

            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>

        <div class="bg-blue-600 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden font-kanit">
          <div class="absolute -right-4 -top-4 opacity-10 text-8xl">
            <i class="fas fa-calculator"></i>
          </div>
          <div class="space-y-4 relative z-10">
            <div class="flex justify-between text-sm opacity-80">
              <span>มูลค่าสินค้า (Subtotal)</span>
              <span id="display-subtotal">0.00</span>
            </div>
            <div class="flex justify-between text-sm opacity-80">
              <span>ภาษีมูลค่าเพิ่ม (VAT 7%)</span>
              <span id="display-vat">0.00</span>
            </div>
            <div class="border-t border-blue-400/50 pt-4 flex justify-between items-end">
              <div>
                <span class="block text-xs opacity-70">ยอดชำระสุทธิ</span>
                <span class="text-3xl font-bold tracking-tight" id="display-total">0.00</span>
              </div>
              <span class="text-xs font-bold bg-blue-500 px-2 py-1 rounded-md uppercase tracking-tighter">THB</span>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider font-kanit">หมายเหตุ (Note)</label>
          <textarea name="note" rows="2" class="w-full rounded-xl border-gray-200 text-sm" placeholder="ระบุหมายเหตุแนบท้ายเอกสาร...">{{ $sale->note }}</textarea>
        </div>
      </div>
    </div>
  </form>

  <script>
    // 1. เริ่มต้น rowCount จากจำนวนรายการที่มีอยู่จริงใน Blade
    let rowCount = {{ $sale->items->count() }};

    function updateCustomerInfo(select) {
      const selectedOption = select.options[select.selectedIndex];
      document.getElementById('tax_id').value = selectedOption.getAttribute('data-tax') || '';
      document.getElementById('address').value = selectedOption.getAttribute('data-address') || '';
    }

    function addRow() {
      const tbody = document.getElementById('item-tbody');
      const newRow = document.createElement('tr');
      newRow.className = "bg-white border border-gray-100 rounded-lg shadow-sm item-row";

      newRow.innerHTML = `
            <td class="py-3 px-4">
                <input type="text" name="items[${rowCount}][desc]" class="w-full border-none focus:ring-0 text-sm p-0 font-medium" placeholder="ชื่อสินค้าหรือบริการ...">
            </td>
            <td class="py-3 px-4">
                <input type="number" name="items[${rowCount}][qty]" value="1" min="1" oninput="calculateTotal()" class="qty-input w-full border-none focus:ring-0 text-sm text-center p-0 font-bold">
            </td>
            <td class="py-3 px-4">
                <input type="number" step="0.01" name="items[${rowCount}][price]" value="0.00" oninput="calculateTotal()" class="price-input w-full border-none focus:ring-0 text-sm text-right p-0 font-bold text-blue-600">
            </td>
            <td class="py-3 px-4 text-right text-sm font-bold text-gray-700 row-total">0.00</td>
            <td class="py-3 text-center">
                <button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500 transition-colors">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </td>
        `;
      tbody.appendChild(newRow);
      rowCount++;
      calculateTotal();
    }

    function removeRow(button) {
      const rows = document.querySelectorAll('.item-row');
      if (rows.length > 1) {
        button.closest('tr').remove();
        reIndexRows();
        calculateTotal();
      } else {
        alert('อย่างน้อยต้องมี 1 รายการครับ');
      }
    }

    function reIndexRows() {
      const rows = document.querySelectorAll('.item-row');
      rows.forEach((row, index) => {
        row.querySelector('input[name*="[desc]"]').name = `items[${index}][desc]`;
        row.querySelector('input[name*="[qty]"]').name = `items[${index}][qty]`;
        row.querySelector('input[name*="[price]"]').name = `items[${index}][price]`;
      });
      rowCount = rows.length;
    }

    // --- ส่วนที่แก้ไขให้ VAT ทำงานแน่นอน ---
    function calculateTotal() {
      let subtotal = 0;
      const rows = document.querySelectorAll('.item-row');

      rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = qty * price;

        // อัปเดตช่อง "รวมเงิน" ของแต่ละบรรทัด
        row.querySelector('.row-total').innerText = total.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        subtotal += total;
      });

      // ดึงค่าจาก Toggle (ตรวจสอบว่า Checked หรือไม่)
      const vatToggle = document.getElementById('vat_toggle');
      const isVatEnabled = vatToggle && vatToggle.checked;

      const vatRate = isVatEnabled ? 0.07 : 0;
      const vatAmount = subtotal * vatRate;
      const grandTotal = subtotal + vatAmount;

      // แสดงผลใน Side Panel
      document.getElementById('display-subtotal').innerText = subtotal.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      document.getElementById('display-vat').innerText = vatAmount.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      document.getElementById('display-total').innerText = grandTotal.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }

    // ใช้ DOMContentLoaded แทน window.onload เพื่อความชัวร์ในการจับ Element
    document.addEventListener('DOMContentLoaded', function() {
      calculateTotal();
    });
  </script>
</x-app-layout>
