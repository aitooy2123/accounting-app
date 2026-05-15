@extends('layouts.app')

@section('content')

<div class="p-6 max-w-5xl mx-auto">

    <div class="bg-white rounded-2xl shadow p-6">

        <h1 class="text-2xl font-bold mb-6">
            แก้ไขค่าใช้จ่าย
        </h1>

        <form method="POST"
              action="{{ route('expenses.update', $expense) }}">

            @csrf
            @method('PUT')

            @include('pages.expenses.form')

            <div class="mt-6">
                <button class="bg-yellow-500 text-white px-6 py-2 rounded-lg">
                    อัปเดต
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
