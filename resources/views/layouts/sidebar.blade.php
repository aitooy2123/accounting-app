<aside class="w-72 bg-white border-r border-gray-200 flex flex-col z-50">
  <div class="p-6">
    <div class="flex items-center space-x-3 text-blue-600">
      <i class="fas fa-cube text-3xl"></i>
      <span class="text-xl font-bold tracking-tight">ระบบบัญชี ACC</span>
    </div>
  </div>

  <nav class="flex-1 px-4 space-y-1">
    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase">ธุรกรรมหลัก</div>

    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fas fa-th-large">
      ภาพรวม (Dashboard)
    </x-sidebar-link>

    <x-sidebar-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" icon="fas fa-file-invoice-dollar">
      ขาย (Sales)
    </x-sidebar-link>

    <x-sidebar-link :href="route('purchases')" :active="request()->routeIs('purchases')" icon="fas fa-shopping-cart">
      ซื้อ (Purchases)
    </x-sidebar-link>

    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase">การเงิน & บัญชี</div>

    <x-sidebar-link :href="route('banks')" :active="request()->routeIs('banks')" icon="fas fa-university">
      ธนาคาร (Banking)
    </x-sidebar-link>

    <x-sidebar-link :href="route('accounting')" :active="request()->routeIs('accounting')" icon="fas fa-book">
      บัญชี (Accounting)
    </x-sidebar-link>

    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase">ฐานข้อมูล</div>

    <x-sidebar-link :href="route('customers')" :active="request()->routeIs('customers')" icon="fas fa-users">
      รายชื่อลูกค้า (Customers)
    </x-sidebar-link>

    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase">ตั้งค่าระบบ</div>

    <x-sidebar-link :href="route('companies.index')" :active="request()->routeIs('companies.*')" icon="fas fa-building">
      ข้อมูลบริษัท
    </x-sidebar-link>

    <x-sidebar-link :href="route('branches.index')" :active="request()->routeIs('branches.*')" icon="fas fa-network-wired">
      สาขา (Branches)
    </x-sidebar-link>
  </nav>

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
