<x-app-layout>
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-xl text-gray-800">บัญชีธนาคาร</h3>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all">
            <i class="fas fa-university mr-2"></i> เชื่อมต่อบัญชีใหม่
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($bankAccounts as $bank)
            <div class="group relative overflow-hidden p-6 rounded-2xl text-white shadow-lg transition-transform hover:-translate-y-1 {{ $bank->color_class }}">
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-bold opacity-80 uppercase">{{ $bank->bank_name }}</p>
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="text-lg font-bold mt-4">{{ $bank->account_number }}</p>
                    <p class="text-xs opacity-80">{{ $bank->account_type }}</p>
                    <div class="mt-8">
                        <p class="text-xs opacity-80">ยอดเงินคงเหลือ</p>
                        <p class="text-2xl font-bold">฿ {{ number_format($bank->balance, 2) }}</p>
                    </div>
                </div>
                <i class="fas fa-university absolute -bottom-4 -right-4 text-white opacity-10 text-9xl"></i>
            </div>
        @empty
            <div class="col-span-3 border-2 border-dashed border-gray-200 p-12 rounded-2xl flex flex-col items-center justify-center text-gray-400">
                <p>ยังไม่มีข้อมูลบัญชีธนาคาร</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
