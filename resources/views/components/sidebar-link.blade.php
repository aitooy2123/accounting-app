@props(['active', 'href', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center p-3 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 border-r-4 border-blue-700'
            : 'flex items-center p-3 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <i class="{{ $icon }} w-6"></i>
    <span class="ml-3">{{ $slot }}</span>
</a>
