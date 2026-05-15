@extends('layouts.app')

@section('content')

<div class="p-6 max-w-5xl mx-auto">

    <div class="bg-white rounded-2xl shadow p-6">

        <h1 class="text-2xl font-bold mb-6">
            เพิ่มค่าใช้จ่าย
        </h1>

        <form method="POST"
              action="{{ route('expenses.store') }}">

            @csrf

            @include('pages.expenses.form')

            <div class="mt-6">
                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    บันทึก
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
