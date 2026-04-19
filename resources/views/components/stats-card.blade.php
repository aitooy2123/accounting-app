@props(['title', 'amount', 'color' => 'blue'])

@php
    // กำหนดสีตามเงื่อนไข
    $colors = [
        'blue' => 'text-blue-600',
        'green' => 'text-green-600',
        'red' => 'text-red-600'
    ];
    $textColor = $colors[$color] ?? 'text-gray-600';
@endphp

<div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
    <p class="text-sm text-gray-500">{{ $title }}</p>
    <h4 class="text-2xl font-bold {{ $textColor }}">฿ {{ number_format($amount) }}</h4>
</div>
