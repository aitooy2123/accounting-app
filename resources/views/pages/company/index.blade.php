@extends('layouts.app')  {{-- adjust to your layout --}}

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Companies</h1>
        <a href="{{ route('companies.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Add Company
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
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Phone</th>
                    <th class="px-4 py-2 text-left">Tax ID</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $company->id }}</td>
                    <td class="px-4 py-2">{{ $company->name }}</td>
                    <td class="px-4 py-2">{{ $company->email }}</td>
                    <td class="px-4 py-2">{{ $company->phone }}</td>
                    <td class="px-4 py-2">{{ $company->tax_id }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <a href="{{ route('companies.edit', $company) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                        <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline-block"
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No companies found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $companies->links() }}
    </div>
</div>
@endsection
