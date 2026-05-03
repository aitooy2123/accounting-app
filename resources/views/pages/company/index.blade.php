@extends('layouts.app')

@section('content')
  <div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 font-kanit">จัดการบริษัท</h1>
        <p class="text-sm text-gray-500 font-kanit">จัดการข้อมูลบริษัทและสถานะการใช้งาน</p>
      </div>

      <div class="flex items-center space-x-3">
        {{-- BULK DELETE BUTTON --}}
        <button type="button"
                id="bulkDeleteBtn"
                class="hidden inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-red-200/50 font-kanit"
                onclick="bulkDelete()">
          <i class="fas fa-trash-alt mr-2"></i>
          <span id="bulkDeleteText">ลบที่เลือก (0)</span>
        </button>

        <a href="{{ route('companies.create') }}"
           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-blue-200/50 hover:shadow-blue-300/50 transform hover:-translate-y-0.5 font-kanit">
          <i class="fas fa-plus-circle mr-2"></i> เพิ่มบริษัทใหม่
        </a>
      </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
      <form method="GET" action="{{ route('companies.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- SEARCH --}}
        <div class="md:col-span-2 relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            <i class="fas fa-search"></i>
          </div>
          <input type="text"
                 name="search"
                 value="{{ request('search') }}"
                 class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-kanit"
                 placeholder="ค้นหาชื่อบริษัท อีเมล เบอร์โทร เลขภาษี...">
        </div>

        {{-- STATUS FILTER --}}
        <select name="status"
                class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-kanit">
          <option value="">ทุกสถานะ</option>
          <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>เปิดใช้งาน</option>
          <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
        </select>

        {{-- BUTTONS --}}
        <div class="flex space-x-2">
          <button type="submit"
                  class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 font-kanit">
            <i class="fas fa-filter mr-2"></i>กรองข้อมูล
          </button>
          <a href="{{ route('companies.index') }}"
             class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all duration-300 flex items-center justify-center">
            <i class="fas fa-redo-alt"></i>
          </a>
        </div>
      </form>
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

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left font-kanit">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <tr>
              {{-- CHECKBOX --}}
              <th class="px-6 py-4 w-12">
                <div class="flex items-center">
                  <input type="checkbox"
                         id="selectAllCheckbox"
                         class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                </div>
              </th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">รหัส</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ชื่อบริษัท</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ข้อมูลติดต่อ</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">เลขผู้เสียภาษี</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">สถานะ</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">จัดการ</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($companies as $company)
              <tr class="hover:bg-blue-50/30 transition-all duration-200 group {{ $company->is_active ? '' : 'opacity-60' }}">
                {{-- CHECKBOX --}}
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <input type="checkbox"
                           class="company-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                           value="{{ $company->id }}"
                           data-company-id="{{ $company->id }}"
                           data-company-name="{{ $company->name }}">
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="text-sm font-bold text-blue-600 font-mono bg-blue-50 px-2 py-1 rounded-lg">
                    {{ $company->code ?? 'N/A' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                      {{ mb_substr($company->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-semibold text-gray-900">{{ $company->name }}</div>
                      <div class="text-xs text-gray-400">สร้างเมื่อ {{ $company->created_at->format('d/m/Y') }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="space-y-1">
                    @if($company->email)
                      <div class="flex items-center text-sm text-gray-600">
                        <i class="far fa-envelope text-blue-400 mr-2 text-xs w-4"></i>
                        <span class="truncate max-w-[200px]">{{ $company->email }}</span>
                      </div>
                    @endif
                    @if($company->phone)
                      <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-phone-alt text-green-400 mr-2 text-xs w-4"></i>
                        {{ $company->phone }}
                      </div>
                    @endif
                    @if(!$company->email && !$company->phone)
                      <span class="text-gray-400 text-sm">-</span>
                    @endif
                  </div>
                </td>
                <td class="px-6 py-4">
                  @if($company->tax_id)
                    <span class="inline-flex items-center px-2.5 py-1 bg-purple-50 text-purple-700 rounded-lg text-sm font-mono font-semibold">
                      <i class="fas fa-file-invoice mr-1.5 text-xs"></i>
                      {{ $company->tax_id }}
                    </span>
                  @else
                    <span class="text-gray-400 text-sm">-</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-center">
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox"
                           class="sr-only peer toggle-status"
                           data-company-id="{{ $company->id }}"
                           {{ $company->is_active ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                    </div>
                  </label>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end items-center space-x-1">
                    <a href="{{ route('companies.edit', $company) }}"
                       class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                       title="แก้ไขข้อมูล">
                      <i class="fas fa-pencil-alt text-sm"></i>
                    </a>
                    <button type="button"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 delete-company"
                            data-company-id="{{ $company->id }}"
                            data-company-name="{{ $company->name }}"
                            title="ลบบริษัท">
                      <i class="fas fa-trash-alt text-sm"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-6 py-20 text-center">
                  <div class="flex flex-col items-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                      <i class="fas fa-building text-4xl text-gray-300"></i>
                    </div>
                    <span class="font-kanit text-gray-500 font-medium">ยังไม่มีข้อมูลบริษัทในระบบ</span>
                    <a href="{{ route('companies.create') }}"
                       class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-all duration-300">
                      <i class="fas fa-plus mr-2"></i>เพิ่มบริษัทแรกของคุณที่นี่
                    </a>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($companies->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
          {{ $companies->appends(request()->query())->links() }}
        </div>
      @endif
    </div>
  </div>

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
      const companyCheckboxes = document.querySelectorAll('.company-checkbox');
      const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
      const bulkDeleteText = document.getElementById('bulkDeleteText');
      const selectionBar = document.getElementById('selectionBar');
      const selectedCountDisplay = document.getElementById('selectedCountDisplay');

      function updateSelectionUI() {
        const checkedCheckboxes = document.querySelectorAll('.company-checkbox:checked');
        const count = checkedCheckboxes.length;

        selectedCountDisplay.textContent = count;

        if (count > 0) {
          bulkDeleteBtn.classList.remove('hidden');
          selectionBar.classList.remove('hidden');
          bulkDeleteText.textContent = `ลบที่เลือก (${count})`;
        } else {
          bulkDeleteBtn.classList.add('hidden');
          selectionBar.classList.add('hidden');
        }

        const allCheckboxes = document.querySelectorAll('.company-checkbox');
        const allChecked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
      }

      window.clearSelection = function() {
        companyCheckboxes.forEach(checkbox => {
          checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateSelectionUI();
      };

      selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        companyCheckboxes.forEach(checkbox => {
          checkbox.checked = isChecked;
        });
        updateSelectionUI();
      });

      companyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionUI);
      });

      window.bulkDelete = function() {
        const checkedCheckboxes = document.querySelectorAll('.company-checkbox:checked');
        const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value);
        const selectedNames = Array.from(checkedCheckboxes).map(cb => cb.dataset.companyName);

        if (selectedIds.length === 0) {
          Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกบริษัท',
            text: 'คุณต้องเลือกอย่างน้อย 1 รายการเพื่อลบ',
            customClass: { popup: 'font-kanit' }
          });
          return;
        }

        const nameListHtml = selectedNames.slice(0, 5).map(name =>
          `<li class="text-sm"><i class="fas fa-building mr-2 text-red-400"></i>${name}</li>`
        ).join('');
        const moreCount = selectedNames.length > 5 ?
          `<li class="text-sm text-gray-400 italic">...และอีก ${selectedNames.length - 5} รายการ</li>` : '';

        Swal.fire({
          title: 'ยืนยันการลบหลายรายการ',
          html: `
            <div class="text-left">
              <p class="mb-3">คุณต้องการลบบริษัททั้งหมด <strong>${selectedIds.length} รายการ</strong> ใช่หรือไม่?</p>
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                <ul class="space-y-1">
                  ${nameListHtml}
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
            Swal.fire({
              title: 'กำลังดำเนินการ...',
              html: 'กำลังลบบริษัทที่เลือก กรุณารอสักครู่',
              allowOutsideClick: false,
              didOpen: () => { Swal.showLoading(); },
              customClass: { popup: 'font-kanit' }
            });

            fetch('{{ route("companies.bulk-delete") }}', {
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
                  text: data.message,
                  timer: 3000,
                  showConfirmButton: false,
                  customClass: { popup: 'font-kanit' }
                });
                setTimeout(() => { window.location.reload(); }, 1500);
              } else {
                throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบ');
              }
            })
            .catch(error => {
              Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: error.message,
                confirmButtonText: 'ตกลง',
                customClass: { popup: 'font-kanit' }
              });
            });
          }
        });
      };

      // ============= TOGGLE STATUS =============

      document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
          const companyId = this.dataset.companyId;
          const isActive = this.checked;

          fetch(`/companies/${companyId}/toggle-status`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ is_active: isActive })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'อัปเดตสถานะเรียบร้อย',
                showConfirmButton: false,
                timer: 1500,
                customClass: { popup: 'font-kanit' }
              });
              setTimeout(() => location.reload(), 1600);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            this.checked = !isActive;
            Swal.fire({
              icon: 'error',
              title: 'เกิดข้อผิดพลาด',
              text: 'ไม่สามารถอัปเดตสถานะได้',
              customClass: { popup: 'font-kanit' }
            });
          });
        });
      });

      // ============= SINGLE DELETE =============

      document.querySelectorAll('.delete-company').forEach(button => {
        button.addEventListener('click', function() {
          const companyId = this.dataset.companyId;
          const companyName = this.dataset.companyName;

          Swal.fire({
            title: 'ยืนยันการลบบริษัท?',
            html: `คุณต้องการลบบริษัท <strong>${companyName}</strong> ใช่หรือไม่?<br>
                   <span class="text-red-500 text-sm">การกระทำนี้ไม่สามารถย้อนกลับได้</span>`,
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
              form.action = `/companies/${companyId}`;
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

    tr:has(.company-checkbox:checked) {
      background-color: #EFF6FF;
    }

    .toggle-status + div {
      transition: all 0.3s ease;
    }

    .toggle-status:checked + div {
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
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

    #selectAllCheckbox:indeterminate {
      background-color: #3B82F6;
      border-color: #3B82F6;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z' clip-rule='evenodd'/%3E%3Csvg%3E");
    }
  </style>
@endsection
