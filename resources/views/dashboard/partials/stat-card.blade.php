{{-- resources/views/dashboard/partials/stat-card.blade.php --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-kanit">{{ $title }}</p>
            <p class="text-2xl font-bold text-{{ $color }}-600 font-kanit mt-1">{{ $value }}</p>
        </div>
        <div class="w-12 h-12 bg-{{ $color }}-100 rounded-xl flex items-center justify-center">
            <i class="fas {{ $icon }} text-{{ $color }}-600 text-xl"></i>
        </div>
    </div>
    <p class="text-xs text-gray-400 mt-2 font-kanit">{{ $subtitle }}</p>
</div>
