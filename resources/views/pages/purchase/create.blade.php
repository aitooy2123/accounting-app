<x-app-layout>
  {{-- ส่วนแสดง Error Validation --}}
  @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
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

  <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
    @csrf

    {{-- ส่งค่าคำนวณเงินผ่าน Hidden Inputs เพื่อให้ Controller นำไปบันทึก --}}
    <input type="hidden" name="subtotal" id="input-subtotal" value="{{ old('subtotal', 0) }}">
    <input type="hidden" name="vat" id="input-vat" value="{{ old('vat', 0) }}">
    <input type="hidden" name="total" id="input-total" value="{{ old('total', 0) }}">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 font-kanit">สร้างใบสั่งซื้อ / บันทึกการซื้อ</h1>
        <p class="text-sm text-gray-500 font-kanit">บันทึกรายการซื้อสินค้าเข้าสต็อกและจัดการยอดค้างชำระ</p>
      </div>
      <div class="flex space-x-3">
        <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">ยกเลิก</a>
        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-emerald-200 font-kanit">
          <i class="fas fa-save mr-2"></i> บันทึกรายการซื้อ
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-6">

        {{-- ข้อมูลผู้จำหน่ายและสาขา --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="flex items-center space-x-2 mb-6 text-emerald-600 font-bold text-lg border-b pb-4 font-kanit">
            <i class="fas fa-truck-loading"></i>
            <span>ข้อมูลผู้จำหน่าย (Supplier)</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลือกผู้จำหน่าย / เจ้าหนี้</label>
              <select name="supplier_id" class="w-full rounded-xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5 @error('supplier_id') border-red-500 @enderror">
                <option value="">-- เลือกรายชื่อผู้จำหน่าย --</option>
                @foreach ($suppliers as $supplier)
                  <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">เลขที่เอกสาร (Doc No.)</label>
              <input type="text" name="doc_no" value="{{ old('doc_no', $autoDocNo) }}" readonly
                     class="w-full rounded-xl border-gray-200 bg-gray-100 text-sm py-2.5 font-bold text-blue-600 cursor-not-allowed">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">รับเข้าสาขา</label>
              <select name="branch_id" class="w-full rounded-xl border-gray-200 text-sm py-2.5">
                @foreach ($branches as $branch)
                  <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">หมายเหตุ (Note)</label>
              <textarea name="note" rows="2" class="w-full rounded-xl border-gray-200 text-sm" placeholder="ระบุรายละเอียดเพิ่มเติม...">{{ old('note') }}</textarea>
            </div>
          </div>
        </div>

        {{-- รายการสินค้า --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="flex items-center justify-between mb-4 text-emerald-600 font-bold text-lg font-kanit">
            <div class="flex items-center space-x-2">
              <i class="fas fa-shopping-basket"></i>
              <span>รายการสินค้าที่สั่งซื้อ</span>
            </div>
            <button type="button" onclick="addRow()" class="text-xs bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition-all font-bold">
              <i class="fas fa-plus mr-1"></i> เพิ่มรายการ
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full border-separate border-spacing-y-2" id="itemsTable">
              <thead class="bg-gray-50 rounded-lg">
                <tr>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-left tracking-widest">รายละเอียด</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center w-24">จำนวน</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32">ทุน/หน่วย</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32">รวมเงิน</th>
                  <th class="w-10"></th>
                </tr>
              </thead>
              <tbody id="item-tbody">
                @php $oldItems = old('items', [['desc' => '', 'qty' => 1, 'price' => 0]]); @endphp
                @foreach ($oldItems as $index => $item)
                <tr class="bg-white border border-gray-100 rounded-lg shadow-sm item-row">
                  <td class="py-3 px-4">
                    <input type="text" name="items[{{ $index }}][desc]" value="{{ $item['desc'] ?? '' }}" class="w-full border-none focus:ring-0 text-sm p-0 font-medium" placeholder="ระบุรายการสินค้า...">
                  </td>
                  <td class="py-3 px-4">
                    <input type="number" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? 1 }}" min="1" oninput="calculateTotal()" class="qty-input w-full border-none focus:ring-0 text-sm text-center p-0 font-bold">
                  </td>
                  <td class="py-3 px-4">
                    <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item['price'] ?? 0 }}" oninput="calculateTotal()" class="price-input w-full border-none focus:ring-0 text-sm text-right p-0 font-bold text-emerald-600">
                  </td>
                  <td class="py-3 px-4 text-right text-sm font-bold text-gray-700 row-total">0.00</td>
                  <td class="py-3 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500">
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

      {{-- Sidebar ขวา --}}
      <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 font-kanit">
          <div class="flex items-center space-x-2 mb-4 text-emerald-600 font-bold text-lg">
            <i class="fas fa-calendar-check"></i>
            <span>กำหนดเวลา</span>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">วันที่เอกสาร (Doc Date)</label>
              <input type="date" name="doc_date" value="{{ old('doc_date', date('Y-m-d')) }}" class="w-full rounded-xl border-gray-200 text-sm py-2">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">วันที่ครบกำหนด (Due Date)</label>
              <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" class="w-full rounded-xl border-gray-200 text-sm py-2">
            </div>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 font-kanit">
          <div class="flex items-center space-x-2 mb-3">
            <i class="fas fa-percent text-emerald-600"></i>
            <span class="text-sm font-bold text-gray-700">ภาษีมูลค่าเพิ่ม (VAT)</span>
          </div>
          <div class="flex flex-col space-y-2">
            @foreach([0, 7, 10] as $v)
              <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="vat_rate" value="{{ $v }}" onchange="calculateTotal()" class="text-emerald-600" {{ old('vat_rate', '7') == $v ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">VAT {{ $v }}%</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="bg-emerald-600 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden font-kanit">
          <div class="absolute -right-4 -top-4 opacity-10 text-8xl">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div class="space-y-4 relative z-10">
            <div class="flex justify-between text-sm opacity-80">
              <span>ยอดรวมสินค้า</span>
              <span id="display-subtotal">0.00</span>
            </div>
            <div class="flex justify-between text-sm opacity-80">
              <span id="vat-label">ภาษี (7%)</span>
              <span id="display-vat">0.00</span>
            </div>
            <div class="border-t border-emerald-400/50 pt-4 flex justify-between items-end">
              <div>
                <span class="block text-xs opacity-70">ยอดชำระสุทธิ</span>
                <span class="text-3xl font-bold tracking-tight" id="display-total">0.00</span>
              </div>
              <span class="text-xs font-bold bg-emerald-500 px-2 py-1 rounded-md uppercase">THB</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script>
    let rowCount = {{ count(old('items', [['desc' => '', 'qty' => 1, 'price' => 0]])) }};

    function addRow() {
      const tbody = document.getElementById('item-tbody');
      const newRow = document.createElement('tr');
      newRow.className = "bg-white border border-gray-100 rounded-lg shadow-sm item-row";
      newRow.innerHTML = `
        <td class="py-3 px-4"><input type="text" name="items[${rowCount}][desc]" class="w-full border-none focus:ring-0 text-sm p-0 font-medium" placeholder="ชื่อสินค้า..."></td>
        <td class="py-3 px-4"><input type="number" name="items[${rowCount}][qty]" value="1" min="1" oninput="calculateTotal()" class="qty-input w-full border-none focus:ring-0 text-sm text-center p-0 font-bold"></td>
        <td class="py-3 px-4"><input type="number" step="0.01" name="items[${rowCount}][price]" value="0.00" oninput="calculateTotal()" class="price-input w-full border-none focus:ring-0 text-sm text-right p-0 font-bold text-emerald-600"></td>
        <td class="py-3 px-4 text-right text-sm font-bold text-gray-700 row-total">0.00</td>
        <td class="py-3 text-center"><button type="button" onclick="removeRow(this)" class="text-gray-300 hover:text-red-500"><i class="fas fa-trash-alt text-xs"></i></button></td>
      `;
      tbody.appendChild(newRow);
      rowCount++;
      calculateTotal();
    }

    function removeRow(btn) {
      if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('tr').remove();
        calculateTotal();
      }
    }

    function calculateTotal() {
      let subtotal = 0;
      document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const rowTotal = qty * price;
        row.querySelector('.row-total').innerText = rowTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
        subtotal += rowTotal;
      });

      const vatRateElem = document.querySelector('input[name="vat_rate"]:checked');
      const vatPercent = vatRateElem ? parseFloat(vatRateElem.value) : 0;
      const vat = subtotal * (vatPercent / 100);
      const total = subtotal + vat;

      document.getElementById('vat-label').innerText = `ภาษี (${vatPercent}%)`;
      document.getElementById('display-subtotal').innerText = subtotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
      document.getElementById('display-vat').innerText = vat.toLocaleString(undefined, { minimumFractionDigits: 2 });
      document.getElementById('display-total').innerText = total.toLocaleString(undefined, { minimumFractionDigits: 2 });

      document.getElementById('input-subtotal').value = subtotal.toFixed(2);
      document.getElementById('input-vat').value = vat.toFixed(2);
      document.getElementById('input-total').value = total.toFixed(2);
    }

    window.onload = calculateTotal;
  </script>
</x-app-layout>
