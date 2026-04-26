@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">จัดการลูกค้า</h1>
        <a href="{{ route('customers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + เพิ่มลูกค้า
        </a>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>Swal.fire({ icon:'success', title:'{{ session('success') }}', showConfirmButton:false, timer:1500 });</script>
    @endif
    @if(session('error'))
        <script>Swal.fire({ icon:'error', title:'{{ session('error') }}', confirmButtonText:'ตกลง' });</script>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">รหัส</th>
                    <th class="px-4 py-2 text-left">ชื่อลูกค้า</th>
                    <th class="px-4 py-2 text-left">อีเมล</th>
                    <th class="px-4 py-2 text-left">เบอร์โทร</th>
                    <th class="px-4 py-2 text-left">เลขผู้เสียภาษี</th>
                    <th class="px-4 py-2 text-left">สาขา</th>
                    <th class="px-4 py-2 text-left">บริษัท</th>
                    <th class="px-4 py-2 text-center">สถานะ</th>
                    <th class="px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $customer->code }}</td>
                    <td class="px-4 py-2">{{ $customer->name }}</td>
                    <td class="px-4 py-2">{{ $customer->email }}</td>
                    <td class="px-4 py-2">{{ $customer->phone }}</td>
                    <td class="px-4 py-2">{{ $customer->tax_id }}</td>
                    <td class="px-4 py-2">{{ $customer->branch->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $customer->company->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $customer->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="text-yellow-600 hover:text-yellow-900">แก้ไข</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-block delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn">ลบ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4">ไม่มีข้อมูลลูกค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>

<script>
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบลูกค้ารายนี้ใช่หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
</script>
@endsection
