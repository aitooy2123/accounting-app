<x-app-layout>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <x-stats-card title="เงินสดและธนาคาร" amount="1450000" color="blue" />
      <x-stats-card title="รายได้ค้างรับ (AR)" amount="240000" color="green" />
      <x-stats-card title="รายจ่ายค้างจ่าย (AP)" amount="85000" color="red" />
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm h-80">
      <canvas id="mainChart"></canvas>
    </div>
  </div>

  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      // ใส่ Logic Chart.js ตรงนี้
    </script>
  @endpush
</x-app-layout>
