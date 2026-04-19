<x-app-layout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-kanit">จัดการรายการขาย</h1>
            <p class="text-sm text-gray-500 font-kanit">ติดตามสถานะใบแจ้งหนี้และการชำระเงินของลูกค้า</p>
        </div>
        <a href="{{ route('pages.sales_create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-blue-200">
            <i class="fas fa-plus-circle mr-2"></i> สร้างเอกสารใหม่
        </a>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('pages.sales') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="ค้นหาเลขที่เอกสาร หรือ ชื่อลูกค้า...">
            </div>

            <select name="status" class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                <option value="">ทุกสถานะการชำระ</option>
                <option value="ชำระแล้ว" {{ request('status') == 'ชำระแล้ว' ? 'selected' : '' }}>ชำระแล้ว</option>
                <option value="ค้างชำระ" {{ request('status') == 'ค้างชำระ' ? 'selected' : '' }}>ค้างชำระ</option>
            </select>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all">
                    กรองข้อมูล
                </button>
                <a href="{{ route('pages.sales') }}" class="px-4 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all flex items-center justify-center">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">วันที่เอกสาร</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">เลขที่</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">ชื่อลูกค้า</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">ยอดเงินรวม</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">สถานะ</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sales as $item)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->date }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-blue-600">{{ $item->doc_no }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->customer }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            ฿ {{ number_format($item->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $item->status == 'ชำระแล้ว' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-2">
                                <a href="{{ route('pages.sales_edit', $item->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>

                                <form action="{{ route('pages.sales_destroy', $item->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบเอกสารนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            <span class="font-kanit text-sm">ไม่พบข้อมูลรายการขาย</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $sales->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>
