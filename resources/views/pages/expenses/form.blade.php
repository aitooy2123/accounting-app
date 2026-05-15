<x-app-layout>
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

  <form action="{{ route('expenses.store') }}" method="POST" id="expenseForm">
    @csrf

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 font-kanit">บันทึกรายจ่ายใหม่</h1>
        <p class="text-sm text-gray-500 font-kanit">บันทึกข้อมูลการจ่ายเงินและจัดหมวดหมู่บัญชี</p>
      </div>
      <div class="flex space-x-3">
        <a href="{{ route('expenses.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">ยกเลิก</a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200 font-kanit">
          <i class="fas fa-save mr-2"></i> บันทึกรายการ
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      {{-- COLUMN LEFT (2/3) --}}
      <div class="lg:col-span-2 space-y-6">
        {{-- ข้อมูลผู้จำหน่ายและหมวดหมู่ --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="flex items-center space-x-2 mb-6 text-blue-600 font-bold text-lg border-b pb-4 font-kanit">
            <i class="fas fa-user-tie"></i>
            <span>รายละเอียดคู่สัญญาและบัญชี</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผู้จำหน่าย / ผู้รับเงิน <span class="text-red-500">*</span></label>
              <select name="payee_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 @error('payee_id') border-red-500 @enderror">
                <option value="">-- เลือกผู้จำหน่าย --</option>
                @foreach ($payees as $payee)
                  <option value="{{ $payee->id }}" {{ old('payee_id') == $payee->id ? 'selected' : '' }}>
                    {{ $payee->name }}
                  </option>
                @endforeach
              </select>
              @error('payee_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">วันที่รายการ <span class="text-red-500">*</span></label>
              <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}"
                     class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 @error('expense_date') border-red-500 @enderror">
              @error('expense_date')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">ผังบัญชี / หมวดหมู่</label>
              <select name="account_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 @error('account_id') border-red-500 @enderror">
                <option value="">-- เลือกผังบัญชี --</option>
                @foreach ($accounts as $acc)
                  <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                    {{ $acc->code }} - {{ $acc->name_th }}
                  </option>
                @endforeach
              </select>
              @error('account_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">สถานะการชำระเงิน <span class="text-red-500">*</span></label>
              <select name="status" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 @error('status') border-red-500 @enderror">
                <option value="paid" {{ old('status', 'paid') == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>ค้างชำระ</option>
                <option value="invoiced" {{ old('status') == 'invoiced' ? 'selected' : '' }}>ออกใบแจ้งหนี้</option>
              </select>
              @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="md:col-span-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">รายละเอียดค่าใช้จ่าย <span class="text-red-500">*</span></label>
              <textarea name="description" rows="2" class="w-full rounded-xl border-gray-200 text-sm @error('description') border-red-500 @enderror" placeholder="ระบุรายละเอียดของค่าใช้จ่าย...">{{ old('description') }}</textarea>
              @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="md:col-span-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">หมายเหตุเพิ่มเติม</label>
              <textarea name="remark" rows="2" class="w-full rounded-xl border-gray-200 text-sm bg-gray-50 @error('remark') border-red-500 @enderror" placeholder="หมายเหตุภายใน หรือข้อมูลอ้างอิงเลขที่เอกสาร...">{{ old('remark') }}</textarea>
              @error('remark')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        {{-- รายการค่าใช้จ่ายย่อย (Dynamic Rows) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="flex items-center justify-between mb-4 text-blue-600 font-bold text-lg font-kanit">
            <div class="flex items-center space-x-2">
              <i class="fas fa-receipt"></i>
              <span>รายการค่าใช้จ่ายย่อย</span>
            </div>
            <button type="button" onclick="addRow()" class="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition-all font-bold">
              <i class="fas fa-plus mr-1"></i> เพิ่มรายการ
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full border-separate border-spacing-y-2" id="itemsTable">
              <thead class="bg-gray-50 rounded-lg">
                <tr>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-left tracking-widest">รายละเอียดค่าใช้จ่าย</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center w-24 tracking-widest">จำนวน</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32 tracking-widest">ราคา/หน่วย</th>
                  <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-right w-32 tracking-widest">รวมเงิน</th>
                  <th class="w-10"></th>
                </tr>
              </thead>
              <tbody id="item-tbody">
                @php
                  $oldItems = old('items', [['desc' => '', 'qty' => 1, 'price' => 0]]);
                  if (empty($oldItems) || count($oldItems) == 0) {
                      $oldItems = [['desc' => '', 'qty' => 1, 'price' => 0]];
                  }
                @endphp
                @foreach ($oldItems as $index => $item)
                <tr class="bg-white border border-gray-100 rounded-lg shadow-sm item-row">
                  <td class="py-3 px-4">
                    <input type="text" name="items[{{ $index }}][desc]" value="{{ $item['desc'] ?? '' }}" class="w-full border-none focus:ring-0 text-sm p-0 font-medium @error("items.$index.desc") border-red-500 @enderror" placeholder="เช่น ค่าไฟฟ้า ค่าน้ำ ค่าวัสดุ...">
                    @error("items.$index.desc")
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                  </td>
                  <td class="py-3 px-4">
                    <input type="number" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? 1 }}" min="1" oninput="calculateTotal()" class="qty-input w-full border-none focus:ring-0 text-sm text-center p-0 font-bold @error("items.$index.qty") border-red-500 @enderror">
                    @error("items.$index.qty")
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                  </td>
                  <td class="py-3 px-4">
                    <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item['price'] ?? 0 }}" oninput="calculateTotal()" class="price-input w-full border-none focus:ring-0 text-sm text-right p-0 font-bold text-blue-600 @error("items.$index.price") border-red-500 @enderror">
                    @error("items.$index.price")
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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

      {{-- COLUMN RIGHT (1/3) Sidebar --}}
      <div class="space-y-6">
        {{-- เลือกอัตรา VAT --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 font-kanit">
          <div class="flex items-center space-x-2 mb-3">
            <i class="fas fa-percent text-blue-600"></i>
            <span class="text-sm font-bold text-gray-700">เลือกอัตราภาษีมูลค่าเพิ่ม (VAT)</span>
          </div>
          <div class="flex flex-col space-y-2">
            <label class="inline-flex items-center">
              <input type="radio" name="vat_rate" value="0" onchange="calculateTotal()" class="form-radio text-blue-600 focus:ring-blue-500" {{ old('vat_rate') == '0' ? 'checked' : '' }}>
              <span class="ml-2 text-sm text-gray-700">VAT 0% (ยกเว้นภาษี)</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="vat_rate" value="7" onchange="calculateTotal()" class="form-radio text-blue-600 focus:ring-blue-500" {{ old('vat_rate', '7') == '7' ? 'checked' : '' }}>
              <span class="ml-2 text-sm text-gray-700">VAT 7% (มาตรฐาน)</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="vat_rate" value="10" onchange="calculateTotal()" class="form-radio text-blue-600 focus:ring-blue-500" {{ old('vat_rate') == '10' ? 'checked' : '' }}>
              <span class="ml-2 text-sm text-gray-700">VAT 10% (กรณีพิเศษ)</span>
            </label>
          </div>
          @error('vat_rate')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- สรุปยอด (เหมือน Sales) --}}
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
              <span id="vat-label">ภาษีมูลค่าเพิ่ม (VAT {{ old('vat_rate', '7') }}%)</span>
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
        <td class="py-3 px-4">
            <input type="text" name="items[${rowCount}][desc]" class="w-full border-none focus:ring-0 text-sm p-0 font-medium" placeholder="เช่น ค่าไฟฟ้า ค่าน้ำ ค่าวัสดุ...">
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
        alert('กรุณามีรายการค่าใช้จ่ายอย่างน้อย 1 รายการ');
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

    function calculateTotal() {
      let subtotal = 0;
      const rows = document.querySelectorAll('.item-row');

      rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = qty * price;
        row.querySelector('.row-total').innerText = total.toLocaleString(undefined, { minimumFractionDigits: 2 });
        subtotal += total;
      });

      const selectedRadio = document.querySelector('input[name="vat_rate"]:checked');
      let vatPercent = selectedRadio ? parseFloat(selectedRadio.value) : 7;
      const vatRate = vatPercent / 100;
      const vat = subtotal * vatRate;
      const grandTotal = subtotal + vat;

      document.getElementById('vat-label').innerHTML = `ภาษีมูลค่าเพิ่ม (VAT ${vatPercent}%)`;
      document.getElementById('display-subtotal').innerText = subtotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
      document.getElementById('display-vat').innerText = vat.toLocaleString(undefined, { minimumFractionDigits: 2 });
      document.getElementById('display-total').innerText = grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
    }

    window.onload = function() {
      calculateTotal();
    };
  </script>
</x-app-layout>
