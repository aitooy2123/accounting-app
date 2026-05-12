<x-app-layout>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold font-kanit">สมุดรายวันจ่าย (PV)</h1>
        <p class="text-sm text-gray-500">จัดการรายการจ่ายทั้งหมด</p>
    </div>

    <div class="flex items-center space-x-3">

        {{-- BULK DELETE --}}
        <button id="bulkDeleteBtn"
            class="hidden bg-red-600 text-white px-4 py-2 rounded-xl font-kanit"
            onclick="bulkDelete()">
            <i class="fas fa-trash mr-1"></i>
            <span id="bulkText">ลบที่เลือก (0)</span>
        </button>

        <a href="{{ route('pv.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-xl font-kanit">
            + สร้าง PV
        </a>
    </div>
</div>

{{-- SELECTION BAR --}}
<div id="selectionBar"
     class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex justify-between items-center">

    <div class="text-blue-700 font-kanit">
        เลือก <strong id="countSelected">0</strong> รายการ
    </div>

    <button onclick="clearSelection()" class="text-blue-600 underline text-sm">
        ยกเลิก
    </button>
</div>

@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-xl">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow border overflow-hidden">

    <div class="overflow-x-auto">
        <table class="w-full text-sm font-kanit">

            <thead class="bg-gray-50 border-b">
                <tr>

                    <th class="p-3 w-10">
                        <input type="checkbox" id="selectAll">
                    </th>

                    <th class="p-3 text-left">เลขที่ PV</th>
                    <th class="p-3 text-left">วันที่</th>
                    <th class="p-3 text-left">รายละเอียด</th>
                    <th class="p-3 text-right">ยอดรวม</th>
                    <th class="p-3 text-right">จัดการ</th>
                </tr>
            </thead>

            <tbody>
            @forelse($vouchers as $pv)
                <tr class="border-b hover:bg-blue-50">

                    {{-- checkbox --}}
                    <td class="p-3">
                        <input type="checkbox"
                               class="pv-checkbox"
                               value="{{ $pv->id }}"
                               data-pv="{{ $pv->pv_no }}">
                    </td>

                    <td class="p-3 font-bold text-blue-600">
                        {{ $pv->pv_no }}
                    </td>

                    <td class="p-3">
                        {{ $pv->pv_date }}
                    </td>

                    <td class="p-3">
                        {{ $pv->note ?? '-' }}
                    </td>

                    <td class="p-3 text-right font-bold">
                        {{ number_format($pv->total_amount, 2) }}
                    </td>

                    <td class="p-3 text-right space-x-2">

                        <a href="{{ route('pv.show', $pv->id) }}"
                           class="text-blue-600">View</a>

                        <a href="{{ route('pv.edit', $pv->id) }}"
                           class="text-yellow-600">Edit</a>

                        <button onclick="singleDelete({{ $pv->id }}, '{{ $pv->pv_no }}')"
                                class="text-red-600">
                            Delete
                        </button>

                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center p-10 text-gray-400">
                        ไม่มีข้อมูล PV
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

</div>

{{-- PAGINATION --}}
<div class="mt-4">
    {{ $vouchers->links() }}
</div>

</x-app-layout>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkboxes = document.querySelectorAll('.pv-checkbox');
    const selectAll = document.getElementById('selectAll');
    const bulkBtn = document.getElementById('bulkDeleteBtn');
    const selectionBar = document.getElementById('selectionBar');
    const countText = document.getElementById('countSelected');
    const bulkText = document.getElementById('bulkText');

    function updateUI() {
        const checked = document.querySelectorAll('.pv-checkbox:checked');
        const count = checked.length;

        countText.textContent = count;
        bulkText.textContent = `ลบที่เลือก (${count})`;

        if (count > 0) {
            bulkBtn.classList.remove('hidden');
            selectionBar.classList.remove('hidden');
        } else {
            bulkBtn.classList.add('hidden');
            selectionBar.classList.add('hidden');
        }

        selectAll.checked = checkboxes.length === count;
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateUI));

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateUI();
    });

    window.clearSelection = function () {
        checkboxes.forEach(cb => cb.checked = false);
        selectAll.checked = false;
        updateUI();
    };

    window.bulkDelete = function () {
        const ids = Array.from(document.querySelectorAll('.pv-checkbox:checked'))
            .map(cb => cb.value);

        if (ids.length === 0) return;

        Swal.fire({
            title: 'ลบหลายรายการ?',
            text: `ต้องการลบ ${ids.length} รายการหรือไม่`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ลบเลย',
        }).then((res) => {
            if (res.isConfirmed) {

                fetch("{{ route('pv.bulk-delete') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids })
                })
                .then(res => res.json())
                .then(() => location.reload());
            }
        });
    };

    window.singleDelete = function (id, no) {

        Swal.fire({
            title: 'ลบเอกสาร?',
            text: no,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ลบ'
        }).then((res) => {
            if (res.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/pv/${id}`;

                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;

                document.body.appendChild(form);
                form.submit();
            }
        });
    };

});
</script>

<style>
input[type="checkbox"] {
    cursor: pointer;
}
</style>
