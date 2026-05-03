<x-app-layout>
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 font-kanit">จัดการรายการขาย</h1>
      <p class="text-sm text-gray-500 font-kanit">ติดตามสถานะใบแจ้งหนี้และการชำระเงินของลูกค้า</p>
    </div>

    <div class="flex items-center space-x-3">
      {{-- BULK DELETE BUTTON - ซ่อนไว้ก่อนจนกว่าจะมีการเลือก --}}
      <button type="button"
              id="bulkDeleteBtn"
              class="hidden inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-red-200/50 font-kanit"
              onclick="bulkDelete()">
        <i class="fas fa-trash-alt mr-2"></i>
        <span id="bulkDeleteText">ลบที่เลือก (0)</span>
      </button>

      <a href="{{ route('sales.create') }}"
         class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-blue-200/50 hover:shadow-blue-300/50 transform hover:-translate-y-0.5 font-kanit">
        <i class="fas fa-plus-circle mr-2"></i> สร้างเอกสารใหม่
      </a>
    </div>
  </div>

  {{-- SELECTION BAR --}}
  <div id="selectionBar" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center justify-between">
    <div class="flex items-center text-blue-700 font-kanit">
      <i class="fas fa-check-circle mr-2"></i>
      <span>เลือก <strong id="selectedCountDisplay">0</strong> รายการ</span>
    </div>
    <button type="button"
            onclick="clearSelection()"
            class="text-sm text-blue-600 hover:text-blue-800 font-kanit underline">
      ยกเลิกการเลือก
    </button>
  </div>

  <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="md:col-span-2 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
          <i class="fas fa-search"></i>
        </div>
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-kanit"
               placeholder="ค้นหาเลขที่เอกสาร...">
      </div>

      <select name="status"
              class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all font-kanit">
        <option value="">ทุกสถานะการชำระ</option>
        <option value="ชำระแล้ว" {{ request('status') == 'ชำระแล้ว' ? 'selected' : '' }}>ชำระแล้ว</option>
        <option value="ค้างชำระ" {{ request('status') == 'ค้างชำระ' ? 'selected' : '' }}>ค้างชำระ</option>
      </select>

      <div class="flex space-x-2">
        <button type="submit"
                class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 font-kanit">
          <i class="fas fa-filter mr-2"></i>กรองข้อมูล
        </button>
        <a href="{{ route('sales.index') }}"
           class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all duration-300 flex items-center justify-center">
          <i class="fas fa-redo-alt"></i>
        </a>
      </div>
    </form>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left font-kanit">
        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
          <tr>
            {{-- CHECKBOX COLUMN HEADER --}}
            <th class="px-6 py-4 w-12">
              <div class="flex items-center">
                <input type="checkbox"
                       id="selectAllCheckbox"
                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
              </div>
            </th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่เอกสาร</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">เลขที่</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">ยอดเงินรวมสุทธิ</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">สถานะ</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">จัดการ</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($sales as $item)
            <tr class="hover:bg-blue-50/30 transition-all duration-200 group">
              {{-- CHECKBOX COLUMN --}}
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <input type="checkbox"
                         class="sale-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                         value="{{ $item->id }}"
                         data-sale-id="{{ $item->id }}"
                         data-doc-no="{{ $item->doc_no }}">
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ \Carbon\Carbon::parse($item->doc_date)->format('d/m/Y') }}
              </td>
              <td class="px-6 py-4">
                <span class="text-sm font-bold text-blue-600">{{ $item->doc_no }}</span>
                <div class="text-xs text-gray-400 mt-1">
                  <i class="fas fa-user mr-1"></i>{{ $item->customer->name ?? 'ไม่มีข้อมูล' }}
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                ฿ {{ number_format($item->total, 2) }}
              </td>
              <td class="px-6 py-4 text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                  {{ $item->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                  <i class="fas {{ $item->status == 'ชำระแล้ว' ? 'fa-check-circle' : 'fa-clock' }} mr-1.5"></i>
                  {{ $item->status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end items-center space-x-1">
                  <a href="{{ route('sales.edit', $item->id) }}"
                     class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                     title="แก้ไข">
                    <i class="fas fa-pencil-alt text-sm"></i>
                  </a>

                  <button type="button"
                          class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 delete-sale"
                          data-sale-id="{{ $item->id }}"
                          data-doc-no="{{ $item->doc_no }}"
                          title="ลบ">
                    <i class="fas fa-trash-alt text-sm"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                <div class="flex flex-col items-center">
                  <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-file-invoice text-4xl text-gray-300"></i>
                  </div>
                  <span class="font-kanit text-gray-500 font-medium">ยังไม่มีรายการขายในระบบ</span>
                  <a href="{{ route('sales.create') }}"
                     class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-all duration-300">
                    <i class="fas fa-plus mr-2"></i>สร้างเอกสารใบแรกของคุณที่นี่
                  </a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($sales->hasPages())
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
        {{ $sales->appends(request()->query())->links() }}
      </div>
    @endif
  </div>
</x-app-layout>

@push('scripts')
  @include('scripts.sweetalert2')
@endpush

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Success Alert
    @if (session('success'))
      Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false,
        customClass: {
          popup: 'font-kanit'
        }
      });
    @endif

    // Error Alert
    @if (session('error'))
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด!',
        text: "{{ session('error') }}",
        confirmButtonText: 'ตกลง',
        customClass: {
          popup: 'font-kanit',
          confirmButton: 'bg-blue-600 text-white px-6 py-2 rounded-lg'
        }
      });
    @endif

    // ============= BULK DELETE FUNCTIONALITY =============

    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const saleCheckboxes = document.querySelectorAll('.sale-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteText = document.getElementById('bulkDeleteText');
    const selectionBar = document.getElementById('selectionBar');
    const selectedCountDisplay = document.getElementById('selectedCountDisplay');

    function updateSelectionUI() {
      const checkedCheckboxes = document.querySelectorAll('.sale-checkbox:checked');
      const count = checkedCheckboxes.length;

      // Update selected count display
      selectedCountDisplay.textContent = count;

      // Show/hide bulk delete button and selection bar
      if (count > 0) {
        bulkDeleteBtn.classList.remove('hidden');
        selectionBar.classList.remove('hidden');
        bulkDeleteText.textContent = `ลบที่เลือก (${count})`;
      } else {
        bulkDeleteBtn.classList.add('hidden');
        selectionBar.classList.add('hidden');
      }

      // Update select all checkbox state
      const allCheckboxes = document.querySelectorAll('.sale-checkbox');
      const allChecked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
      selectAllCheckbox.checked = allChecked;
      selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
    }

    // Clear all selections
    window.clearSelection = function() {
      saleCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
      });
      selectAllCheckbox.checked = false;
      selectAllCheckbox.indeterminate = false;
      updateSelectionUI();
    };

    // Select All / Deselect All
    selectAllCheckbox.addEventListener('change', function() {
      const isChecked = this.checked;
      saleCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
      });
      updateSelectionUI();
    });

    // Individual checkbox change
    saleCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', updateSelectionUI);
    });

    // Bulk Delete Function
    window.bulkDelete = function() {
      const checkedCheckboxes = document.querySelectorAll('.sale-checkbox:checked');
      const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value);

      // Get selected document numbers for display
      const selectedDocNos = Array.from(checkedCheckboxes).map(cb => cb.dataset.docNo);

      if (selectedIds.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'กรุณาเลือกรายการ',
          text: 'คุณต้องเลือกอย่างน้อย 1 รายการเพื่อลบ',
          customClass: {
            popup: 'font-kanit'
          }
        });
        return;
      }

      // Create list of documents to delete
      const docListHtml = selectedDocNos.slice(0, 5).map(doc =>
        `<li class="text-sm"><i class="fas fa-file-invoice mr-2 text-red-400"></i>${doc}</li>`
      ).join('');

      const moreCount = selectedDocNos.length > 5 ?
        `<li class="text-sm text-gray-400 italic">...และอีก ${selectedDocNos.length - 5} รายการ</li>` : '';

      Swal.fire({
        title: 'ยืนยันการลบหลายรายการ',
        html: `
          <div class="text-left">
            <p class="mb-3">คุณต้องการลบเอกสารทั้งหมด <strong>${selectedIds.length} รายการ</strong> ใช่หรือไม่?</p>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
              <ul class="space-y-1">
                ${docListHtml}
                ${moreCount}
              </ul>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
              <p class="text-red-600 text-sm font-bold mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>คำเตือน
              </p>
              <ul class="text-red-500 text-sm list-disc list-inside space-y-1">
                <li>การกระทำนี้ไม่สามารถย้อนกลับได้</li>
                <li>ข้อมูลทั้งหมดที่เกี่ยวข้องจะถูกลบออกจากระบบ</li>
                <li>กรุณาตรวจสอบรายการที่เลือกให้แน่ใจก่อนลบ</li>
              </ul>
            </div>
          </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: `<i class="fas fa-trash-alt mr-2"></i>ลบ ${selectedIds.length} รายการ`,
        cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
        reverseButtons: true,
        customClass: {
          popup: 'font-kanit rounded-2xl',
          confirmButton: 'px-6 py-2.5 rounded-xl font-bold',
          cancelButton: 'px-6 py-2.5 rounded-xl font-bold'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading state
          Swal.fire({
            title: 'กำลังดำเนินการ...',
            html: 'กำลังลบเอกสารที่เลือก กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            },
            customClass: {
              popup: 'font-kanit'
            }
          });

          // Send bulk delete request
          fetch('{{ route("sales.bulk-delete") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: selectedIds })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'ลบสำเร็จ!',
                text: data.message || `ลบเอกสาร ${selectedIds.length} รายการเรียบร้อยแล้ว`,
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                  popup: 'font-kanit'
                }
              });
              // Reload page after success
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            } else {
              throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบ');
            }
          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'เกิดข้อผิดพลาด!',
              text: error.message || 'ไม่สามารถลบเอกสารได้ กรุณาลองใหม่อีกครั้ง',
              confirmButtonText: 'ตกลง',
              customClass: {
                popup: 'font-kanit'
              }
            });
          });
        }
      });
    };

    // ============= SINGLE DELETE FUNCTIONALITY =============

    document.querySelectorAll('.delete-sale').forEach(button => {
      button.addEventListener('click', function() {
        const saleId = this.dataset.saleId;
        const docNo = this.dataset.docNo;

        Swal.fire({
          title: 'ยืนยันการลบเอกสาร?',
          html: `
            <div class="text-left">
              <p class="mb-3">คุณต้องการลบเอกสาร <strong>${docNo}</strong> ใช่หรือไม่?</p>
              <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-red-600 text-sm">
                  <i class="fas fa-exclamation-triangle mr-2"></i>
                  <span class="text-red-500 text-sm">การกระทำนี้ไม่สามารถย้อนกลับได้</span>
                </p>
              </div>
            </div>
          `,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#EF4444',
          cancelButtonColor: '#6B7280',
          confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>ลบ',
          cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
          reverseButtons: true,
          customClass: {
            popup: 'font-kanit rounded-2xl',
            confirmButton: 'px-6 py-2.5 rounded-xl font-bold',
            cancelButton: 'px-6 py-2.5 rounded-xl font-bold'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/sales/${saleId}`;
            form.innerHTML = `
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
          }
        });
      });
    });

    // Initial UI state
    updateSelectionUI();
  });
</script>

<style>
  input[type="checkbox"] {
    cursor: pointer;
  }

  input[type="checkbox"]:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
  }

  tr:has(.sale-checkbox:checked) {
    background-color: #EFF6FF;
  }

  .swal2-popup {
    border-radius: 1rem !important;
    padding: 2rem !important;
  }

  .swal2-title {
    font-size: 1.5rem !important;
  }

  .swal2-confirm, .swal2-cancel {
    font-family: 'Kanit', sans-serif !important;
  }

  #bulkDeleteBtn, #selectionBar {
    transition: all 0.3s ease-in-out;
  }

  /* Checkbox indeterminate state */
  #selectAllCheckbox:indeterminate {
    background-color: #3B82F6;
    border-color: #3B82F6;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z' clip-rule='evenodd'/%3E%3C/svg%3E");
  }
</style>
