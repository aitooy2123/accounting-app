<aside class="w-72 bg-white border-r border-gray-200 flex flex-col z-50">
    {{-- Header Logo --}}
    <div class="p-6">
        <div class="flex items-center space-x-3 text-blue-600">
            <i class="fas fa-cube text-3xl"></i>
            <span class="text-xl font-bold tracking-tight">ระบบบัญชี ACC</span>
        </div>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 px-4 space-y-1">
        @php
            $menuSections = [
                'ธุรกรรมหลัก' => [
                    ['name' => 'ภาพรวม (Dashboard)', 'route' => 'dashboard', 'icon' => 'fas fa-th-large'],
                    ['name' => 'ขาย (Sales)', 'route' => 'sales.index', 'icon' => 'fas fa-file-invoice-dollar'],
                    ['name' => 'ซื้อ (Purchases)', 'route' => 'purchases.index', 'icon' => 'fas fa-shopping-cart'],
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
            <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase">{{ $heading }}</div>
            @foreach ($links as $link)
                <x-sidebar-link
                    :href="route($link['route'])"
                    :active="request()->routeIs($link['route'] . '.*') || request()->routeIs($link['route'])"
                    :icon="$link['icon']"
                >
                    {{ $link['name'] }}
                </x-sidebar-link>
            @endforeach
        @endforeach
    </nav>

    {{-- User & Logout --}}
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
