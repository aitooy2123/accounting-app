<!-- Alpine.js Core + Plugins -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak] { display: none !important; }</style>
<aside class="w-72 bg-white border-r border-gray-200 flex flex-col z-50" x-data="sidebarMenu()">
    {{-- Header Logo --}}
    <div class="p-6">
        <div class="flex items-center space-x-3 text-blue-600">
            <i class="fas fa-cube text-3xl"></i>
            <span class="text-xl font-bold tracking-tight">ระบบบัญชี ACC</span>
        </div>
    </div>

    {{-- Navigation Menu with Dropdown --}}
    <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
        @php
            $menuSections = [
                'ธุรกรรมหลัก' => [
                    ['name' => 'ภาพรวม (Dashboard)', 'route' => 'dashboard', 'icon' => 'fas fa-th-large'],
                    ['name' => 'ขาย (Sales)', 'route' => 'sales.index', 'icon' => 'fas fa-file-invoice-dollar'],
                    ['name' => 'ซื้อ (Purchases)', 'route' => 'purchases.index', 'icon' => 'fas fa-shopping-cart'],
                ],
                   'ค่าใช้จ่าย' => [
                    ['name' => 'ค่าใช้จ่าย (Expenses)', 'route' => 'expenses.index', 'icon' => 'fas fa-shopping-cart'],
                ],
                'การเงิน & บัญชี' => [
                    ['name' => 'ธนาคาร (Banking)', 'route' => 'banks', 'icon' => 'fas fa-university'],
                    ['name' => 'สมุดบัญชีรายวันทั่วไป', 'route' => 'reports.journal', 'icon' => 'fas fa-book'],
                    ['name' => 'ผังบัญชี (Accounting)', 'route' => 'accounts.index', 'icon' => 'fas fa-book'],
                ],
                'ฐานข้อมูล' => [
                    ['name' => 'รายชื่อลูกค้า (Customers)', 'route' => 'customers.index', 'icon' => 'fas fa-users'],
                ],
                'ตั้งค่าระบบ' => [
                    ['name' => 'ข้อมูลบริษัท', 'route' => 'companies.index', 'icon' => 'fas fa-building'],
                    ['name' => 'สาขา (Branches)', 'route' => 'branches.index', 'icon' => 'fas fa-network-wired'],
                ],
            ];
        @endphp

        @foreach ($menuSections as $heading => $links)
            {{-- Section Header (Click to toggle) --}}
            <div class="pt-4 pb-2 px-3 flex items-center justify-between cursor-pointer select-none group"
                 @click="toggleSection('{{ $heading }}')">
                <div class="text-xs font-semibold text-gray-400 uppercase">{{ $heading }}</div>
                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"
                   :class="{ 'rotate-180': openSections['{{ $heading }}'] }"></i>
            </div>

            {{-- Section Links (Show/Hide) --}}
            <div x-show="openSections['{{ $heading }}']"
                 x-collapse.duration.200ms
                 class="space-y-1">
                @foreach ($links as $link)
                    <x-sidebar-link
                        :href="route($link['route'])"
                        :active="request()->routeIs($link['route'] . '.*') || request()->routeIs($link['route'])"
                        :icon="$link['icon']"
                    >
                        {{ $link['name'] }}
                    </x-sidebar-link>
                @endforeach
            </div>
        @endforeach
    </nav>

    {{-- User & Logout (unchanged) --}}
    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex items-center w-full p-3 bg-gray-50 rounded-xl hover:bg-red-50 group">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="ml-3 text-left">
                    <p class="text-xs font-bold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-500">ออกจากระบบ</p>
                </div>
                <i class="fas fa-power-off ml-auto text-gray-300 group-hover:text-red-500"></i>
            </button>
        </form>
    </div>
</aside>

<script>
    function sidebarMenu() {
        return {
            // เก็บสถานะการเปิด-ปิดของแต่ละหมวดหมู่ (default: เปิดหมวดหมู่แรก)
            openSections: {},
            init() {
                // กำหนดค่าเริ่มต้น: เปิดเฉพาะหมวดหมู่ "ธุรกรรมหลัก"
                const sections = @json(array_keys($menuSections));
                sections.forEach((section, index) => {
                    this.openSections[section] = (index === 0); // เปิดเฉพาะหมวดแรก
                });
                // โหลดสถานะจาก localStorage (ถ้าต้องการให้จำ)
                const saved = localStorage.getItem('sidebar_open_sections');
                if (saved) {
                    try {
                        this.openSections = JSON.parse(saved);
                    } catch(e) {}
                }
            },
            toggleSection(section) {
                this.openSections[section] = !this.openSections[section];
                // บันทึกสถานะลง localStorage
                localStorage.setItem('sidebar_open_sections', JSON.stringify(this.openSections));
            }
        }
    }
</script>
