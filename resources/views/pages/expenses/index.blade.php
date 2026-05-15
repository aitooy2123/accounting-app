@extends('layouts.app')

@section('content')

<div class="p-6">

    <div class="flex justify-between mb-4">
        <h1 class="text-2xl font-bold">
            รายการค่าใช้จ่าย
        </h1>

        <a href="{{ route('expenses.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + เพิ่มรายการ
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">วันที่</th>
                    <th class="p-3 text-left">เลขที่เอกสาร</th>
                    <th class="p-3 text-left">ผู้จำหน่าย</th>
                    <th class="p-3 text-left">รายละเอียด</th>
                    <th class="p-3 text-right">จำนวน</th>
                    <th class="p-3 text-left">ผังบัญชี</th>
                    <th class="p-3 text-left">หมายเหตุ</th>
                    <th class="p-3 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @foreach($expenses as $item)
                <tr class="border-t hover:bg-gray-50">

                    <td class="p-3">
                        {{ $item->expense_date }}
                    </td>

                    <td class="p-3 font-medium">
                        {{ $item->doc_no }}
                    </td>

                    <td class="p-3">
                        {{ $item->payee->name ?? '-' }}
                    </td>

                    <td class="p-3">
                        {{ $item->description }}
                    </td>

                    <td class="p-3 text-right text-red-600 font-semibold">
                        {{ number_format($item->amount, 2) }}
                    </td>

                    <td class="p-3">
                        {{ $item->account->account_name ?? '-' }}
                    </td>

                    <td class="p-3">
                        {{ $item->remark }}
                    </td>

                    <td class="p-3">
                        <div class="flex gap-2 justify-center">

                            <a href="{{ route('expenses.edit', $item) }}"
                               class="bg-yellow-500 text-white px-3 py-1 rounded">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('expenses.destroy', $item) }}">
                                @csrf
                                @method('DELETE')

                                <button class="bg-red-600 text-white px-3 py-1 rounded">
                                    Delete
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>

</div>

@endsection
