<x-app-layout>

<div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow">

    <h2 class="text-xl font-bold mb-4">สร้างสมุดรายวันจ่าย (PV)</h2>

    <form action="{{ route('pv.store') }}" method="POST">
        @csrf

        {{-- วันที่ --}}
        <div class="mb-4">
            <label class="block text-sm font-bold">วันที่</label>
            <input type="date" name="pv_date"
                   class="w-full border rounded p-2"
                   required>
        </div>

        {{-- หมายเหตุ --}}
        <div class="mb-4">
            <label class="block text-sm font-bold">หมายเหตุ</label>
            <textarea name="note" class="w-full border rounded p-2"></textarea>
        </div>

        {{-- ตารางรายการ --}}
        <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold">รายการบัญชี</h3>

                <button type="button"
                        onclick="addRow()"
                        class="bg-green-500 text-white px-3 py-1 rounded">
                    + เพิ่มรายการ
                </button>
            </div>

            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">บัญชี</th>
                        <th class="p-2">ประเภท</th>
                        <th class="p-2">จำนวนเงิน</th>
                        <th class="p-2">รายละเอียด</th>
                        <th class="p-2">ลบ</th>
                    </tr>
                </thead>

                <tbody id="pv-items">
                    <tr>
                        <td class="p-2">
                            <select name="items[0][chart_of_account_id]" class="border p-1 w-full">
                                @foreach($accounts ?? [] as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->code }} - {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="p-2">
                            <select name="items[0][type]" class="border p-1 w-full">
                                <option value="dr">Debit</option>
                                <option value="cr">Credit</option>
                            </select>
                        </td>

                        <td class="p-2">
                            <input type="number" step="0.01"
                                   name="items[0][amount]"
                                   class="border p-1 w-full" required>
                        </td>

                        <td class="p-2">
                            <input type="text"
                                   name="items[0][description]"
                                   class="border p-1 w-full">
                        </td>

                        <td class="p-2 text-center">
                            <button type="button"
                                    onclick="removeRow(this)"
                                    class="text-red-500">
                                X
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ปุ่มบันทึก --}}
        <div class="mt-6">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                บันทึก PV
            </button>
        </div>

    </form>
</div>

{{-- JS Dynamic Rows --}}
<script>
let rowIndex = 1;

function addRow() {
    const table = document.getElementById('pv-items');

    const row = document.createElement('tr');

    row.innerHTML = `
        <td class="p-2">
            <select name="items[${rowIndex}][chart_of_account_id]" class="border p-1 w-full">
                @foreach($accounts ?? [] as $acc)
                    <option value="{{ $acc->id }}">
                        {{ $acc->code }} - {{ $acc->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td class="p-2">
            <select name="items[${rowIndex}][type]" class="border p-1 w-full">
                <option value="dr">Debit</option>
                <option value="cr">Credit</option>
            </select>
        </td>

        <td class="p-2">
            <input type="number" step="0.01"
                   name="items[${rowIndex}][amount]"
                   class="border p-1 w-full" required>
        </td>

        <td class="p-2">
            <input type="text"
                   name="items[${rowIndex}][description]"
                   class="border p-1 w-full">
        </td>

        <td class="p-2 text-center">
            <button type="button"
                    onclick="removeRow(this)"
                    class="text-red-500">
                X
            </button>
        </td>
    `;

    table.appendChild(row);
    rowIndex++;
}

function removeRow(btn) {
    btn.closest('tr').remove();
}
</script>

</x-app-layout>
