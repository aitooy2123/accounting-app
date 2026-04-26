@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">จัดการสาขา</h1>
        <a href="{{ route('branches.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + เพิ่มสาขา
        </a>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">รหัส</th>
                    <th class="px-4 py-2 text-left">ชื่อสาขา</th>
                    <th class="px-4 py-2 text-left">โทรศัพท์</th>
                    <th class="px-4 py-2 text-left">ผู้จัดการ</th>
                    <th class="px-4 py-2 text-center">สถานะ</th>
                    <th class="px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $branch->code }}</td>
                    <td class="px-4 py-2">{{ $branch->name }}</td>
                    <td class="px-4 py-2">{{ $branch->phone }}</td>
                    <td class="px-4 py-2">{{ $branch->manager }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $branch->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <a href="{{ route('branches.edit', $branch) }}" class="text-yellow-600 hover:text-yellow-900">แก้ไข</a>
                        <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="inline-block delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn">ลบ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">ไม่มีข้อมูลสาขา</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $branches->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบสาขานี้ใช่หรือไม่?',
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
