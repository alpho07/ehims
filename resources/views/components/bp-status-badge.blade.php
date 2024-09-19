@php
    $status = $get('status');
    $color = match ($status) {
        'normal' => 'bg-green-500',
        'high' => 'bg-red-500',
        'low' => 'bg-yellow-500',
        default => 'bg-gray-500',
    };
@endphp

@if ($status)
    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full text-white {{ $color }}">
        {{ ucfirst($status) }}
    </span>
@endif
